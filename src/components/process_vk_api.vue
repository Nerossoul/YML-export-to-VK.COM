<template>
<div class="card">
    <h2>{{ msg }}</h2>
    <button type="button" class="btn btn-outline-secondary"  @click="marketGet">Button</button>
    <br>
    <pre>{{ responce_text }}</pre>
</div>
</template>

<script>
/* eslint-disable */
export default {
    name: 'vkApi',
  data () {
    return {
      vkApiVersion: '5.92',
      msg: 'i\'m vk api!!!',
      ownApiServer: 'https://playavto.ru/vk_export/',
      responce_text: ''
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
      this._onwApi_call(methodString,params)
    },
    _onwApi_call (method, params) {
      let url = this.ownApiServer
      params.v = this.vkApiVersion
      params.access_token = this.$store.state.access_token
      let vkApiCallObject = {
        method: method,
        params: params
      }
      return fetch(url,{
        method: 'POST',
        body: JSON.stringify(vkApiCallObject),
        mode: 'cors',
        headers: { 'Content-Type': 'application/json; charset=utf-8'}
      }).then((result) => {
        // return result.json()
        return result.text()
      }).then((text)=>{
        this.responce_text = text
        console.log(this.responce_text)
      })
    }
  }
}
</script>

<style scoped>

</style>
