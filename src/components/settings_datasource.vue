<template>
  <div>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Текущий источник данных</h5>
        <p class="card-text">{{ ymlSource }}</p>
        <div v-if="isGettingDataNow && isGettingPhotoBaseNow">{{gettingDataText}}<br>
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
        <div v-else>
          <div v-if="!isData">
            <button type="button" name="button" class="btn btn-dark" @click="getData">Получить данные</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isData" class="card">
      <div class="card-body">
        <h5 class="card-title">Текущие данные от {{ sourceDate }}:</h5>
        <p class="card-text">Категории: {{ categories.length }}</p>
        <p class="card-text">Товары: {{ products.length }}</p>
        <p class="card-text">Товаров с предзагруженными фотографиями: {{ photoBaseObj.length }}</p>
        <router-link tag="button" type="button" class="btn btn-dark" to="/process">
          Go to Process room
        </router-link>
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
      photoBaseSource: 'https://playavto.ru/vk_export/static/api/photobase.json',
      xmlObj: {},
      isGettingDataNow: false,
      isGettingPhotoBaseNow: false,
      gettingDataText: 'Получаем данные...',
      x2js: ''
    }
  },
  watch: {
    xmlObj () {
      this.$store.state.products = this.xmlObj.yml_catalog.shop.offers.offer
      this.$store.state.categories = this.xmlObj.yml_catalog.shop.categories.category
      this.$store.state.sourceDate = this.xmlObj.yml_catalog._date
      document.title = 'VK EXPORT:( 0/' + this.$store.state.products.length + ' )'
    }
  },
  computed: {
    categories () {
      return this.$store.state.categories
    },
    products () {
      return this.$store.state.products
    },
    sourceDate () {
      return this.$store.state.sourceDate
    },
    photoBaseObj () {
      return this.$store.state.photoBase
    },
    isData () {
      return this.$store.state.categories.length > 0 &&
        this.$store.state.products.length > 0
    }
  },
  methods: {
    getData () {
      this.getXmlData()
      this.isGettingDataNow = true
      this.getPhotoBase()
      this.isGettingPhotoBaseNow = true
    },
    getXmlData () {
      let context = this
      fetch(this.ymlSource + '?timestamp=' + new Date().getTime())
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
      }
      fetch(this.photoBaseSource + '?timestamp=' + new Date().getTime(), fetchOptions)
        .then(
          (response) => {
            // console.log (response)
            if (!response.ok) {
              console.log('HTTP error, status = ' + response.status)
              return '{}'
            }
            // console.log('Good response')
            return response.json()
          })
        .then(json => {
          // console.log(json)
          if (typeof json === 'object') {
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
          this.isGettingPhotoBaseNow = false
        })
    }
  },
  mounted: function () {
    this.x2js = new X2JS()
  }
}
</script>

<style>

</style>
