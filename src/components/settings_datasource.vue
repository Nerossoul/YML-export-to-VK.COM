<template>
  <div>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Текущий источник данных</h5>
      <p class="card-text">{{ymlSource}}</p>
      <div v-if="isGettingDataNow">{{gettingDataText}}<br>
        <div class="spinner-border" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div v-else >
        <div v-if="!isData"><button type="button" name="button" class="btn btn-dark" @click="getData">Получить данные</button></div>
      </div>
    </div>
  </div>
  <div v-if="isData" class="card">
    <div class="card-body">
      <h5 class="card-title">Текущие данные от {{ sourceDate }}:</h5>
      <p class="card-text">Категории: {{ categories.length }}</p>
      <p class="card-text">Товары: {{ products.length }}</p>
      <p class="card-text">Фотографий в базе: {{ photoBaseObj.length }}</p>
    </div>
  </div>
</div>
</template>

<script>
/* eslint-disable */
export default {
  name: 'settings_datasource',
  data () {
    return {
      // TODO: ymlSourceLink must be user setable and saved to localstorege
      ymlSource: 'https://playavto.ru/export/yandex_yml.xml',
      photoBaseSource: 'https://playavto.ru/vk_export/server/photobase.json',
      xmlObj: {},
      photoBaseObj: {},
      isGettingDataNow: false,
      gettingDataText: 'Получаем данные...',
      x2js: ' ',
    }
  },
  watch: {
    xmlObj () {
      this.$store.state.products = this.xmlObj.yml_catalog.shop.offers.offer
      this.$store.state.categories = this.xmlObj.yml_catalog.shop.categories.category
      this.$store.state.sourceDate = this.xmlObj.yml_catalog._date
      document.title = 'VK EXPORT:( 0/' +this.$store.state.products.length + ' )' 
    }
  },
  computed: {
    categories () {
      return this.$store.state.categories
    },
    products () {
      return this.$store.state.products
    },
    sourceDate() {
      return this.$store.state.sourceDate
    }, 
    isData () {
      if (this.$store.state.categories.length > 0 && 
          this.$store.state.products.length > 0) {
            return true
          }
      return false
    }
  },
  methods: {
    getData () {
      this.isGettingDataNow = true
      this.getXmlData()
      this.getPhotoBase()
    },
    getXmlData () {
      var context = this
      fetch(this.ymlSource)
        .then((resp) => {
          return resp.text()
        })
        .then(function (data) {
          context.xmlObj = context.x2js.xml_str2json(data)
        })
        .then(function () {
          context.isGettingDataNow = false
        })
    },
    getPhotoBase () {
      let fetchOptions = {
        method: 'GET',
        mode: 'cors',
        cache: 'no-cache'
      };
      fetch(this.photoBaseSource, fetchOptions)
        .then((resp) => {
          return resp.json()
        })
        .then(json => {
          if (typeof json == 'object') {
            return json
          } else {
            return JSON.parse(json)
          }
        })
        .then(obj => {
          let photoCounter = 0 
          for (let key in obj) {
            ++photoCounter
          }
          obj.length = photoCounter
          this.$store.state.photoBase = obj
          this.photoBaseObj = obj
          this.isGettingDataNow = false
        })
    }
  },
  mounted: function () {
/* eslint-disable */
    this.x2js = new X2JS()
  }
}
</script>

<style scoped>

</style>
