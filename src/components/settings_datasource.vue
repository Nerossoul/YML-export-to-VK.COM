<template>
  <div>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Текущий источник данных</h5>
      <p class="card-text">{{ymlSourceLink}}</p>
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
      ymlSourceLink: 'https://playavto.ru/export/yandex_yml.xml',
      xmlObj: {},
      isGettingDataNow: false,
      gettingDataText: 'Получаем данные...',
      x2js: ' ',
      sourceDate: ''
    }
  },
  watch: {
    xmlObj () {
      this.$store.state.products = this.xmlObj.yml_catalog.shop.offers.offer
      this.$store.state.categories = this.xmlObj.yml_catalog.shop.categories.category
      this.sourceDate = this.xmlObj.yml_catalog._date
    }
  },
  computed: {
    categories () {
      return this.$store.state.categories
    },
    products () {
      return this.$store.state.products
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
      this.file_get_contents()
      // https://lage.us/Javascript-File-Get-Contents.html
    },
    file_get_contents () {
      var context = this
      fetch(this.ymlSourceLink)
        .then((resp) => {
          return resp.text()
        })
        .then(function (data) {
          context.xmlObj = context.x2js.xml_str2json(data)
        })
        .then(function () {
          context.isGettingDataNow = false
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
