<template>
<div class="card">
    <h2>{{ msg }}</h2>
    <button type="button" class="btn btn-outline-secondary"  @click="marketGet">Button</button>
</div>
</template>

<script>
/* eslint-disable */
export default {
    name: 'vkApi',
  data () {
    return {
      apiServer: 'https://api.vk.com/method/',
      vkApiVersion: '5.92',
      msg: 'i\'vk api!!!'
    }
  },
  methods: {
    marketGet () {
      let methodString = 'market.get'
      let params = {
        owner_id: '-'+this.$store.state.group_id,
        album_id: 0,
        count: 2,
        offset: 0,
        extended: 0,
      }
      this._vkApi_call(methodString,params)
    },
    _vkApi_call(method, params) {
      let url = this.apiServer + method
      params.v = this.vkApiVersion
      params.access_token = this.$store.state.access_token
      return fetch(url,{
        method: 'POST',
        body: JSON.stringify(params),
        mode: 'no-cors',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'}
      }).then((result) => {
        console.log(result.body)
        return result
      })
    }
  }
}
</script>

<style scoped>

</style>
