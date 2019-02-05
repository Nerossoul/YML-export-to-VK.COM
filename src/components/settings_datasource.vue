<template>
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
        <button type="button" name="button" class="btn btn-dark" @click="getData">Получить данные</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Settings',
  data () {
    return {
      // TODO: ymlSourceLink must be user setable and saved to localstorege
      ymlSourceLink: 'https://playavto.ru/export/yandex_yml.xml',
      data: '',
      isGettingDataNow: false,
      gettingDataText: 'Получаем данные...'
    }
  },
  methods: {
    getData () {
      console.log('get data')
      this.isGettingDataNow = true
      setTimeout(() => { this.isGettingDataNow = false }, 3000)
      //https://lage.us/Javascript-File-Get-Contents.html
    }
  }
}
</script>

<style scoped>

</style>
