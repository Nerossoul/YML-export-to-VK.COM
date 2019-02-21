
<template>
  <div class="card mt-3">
      <h2>{{ msg }}</h2>

      <div class="row mt-2">
        <div class="col-sm">
          <div class="card">
            <button type="button" class="btn btn-outline-secondary" @click="upload_file('https:\\\/\\\/pu.vk.com\\\/c850520\\\/upload.php?act=do_add&mid=27533458&aid=-53&gid=81204119&hash=9ead3841837df05bf1b02cccb4cffed4&rhash=a744c7d9b9b8368d8d25756458064b49&swfupload=1&api=1&market_main_photo=1&_crop=2400,2400,2400', 'https://playavto.ru/image/import_files/91/91ceb3bd-437a-11e8-bd9c-408d5c3aa21e_91ceb3bf-437a-11e8-bd9c-408d5c3aa21e.jpeg')">upload</button>
          </div>
        </div>
        <div class="col-sm">
          <div class="card">
            <button type="button" class="btn btn-outline-secondary"  @click="upload_photoToVkGroupCommonMethod('https://playavto.ru/image/import_files/91/91ceb3bd-437a-11e8-bd9c-408d5c3aa21e_91ceb3bf-437a-11e8-bd9c-408d5c3aa21e.jpeg', 1) ">common file upload</button>
          </div>
        </div>
        <div class="col-sm">
          <div class="card">
            <button type="button" class="btn btn-outline-secondary"  @click="photos_getMarketUploadServer(1)">upload server</button>
          </div>
        </div>
      </div>
      <br>
      {{ responce_text }}
  </div>
</template>

<script>
/* eslint-disable */

export default {
    name: 'vkApi',
  data () {
    return {
      msg: 'vk',
      ownApiServer: 'https://playavto.ru/vk_export/server/',
      responce_text: ''
    }
  },
  methods: {

    market_get () {
      let methodString = 'market.get'
      let params = {
        owner_id: '-'+this.$store.state.group_id,
        album_id: 0,
        count: 2,
        offset: 0,
        extended: 0        
      }
      this._onwApi_call(methodString,params)
    },

    market_getAlbums () {
      let methodString = 'market.getAlbums'
      let params = {
        owner_id: '-'+this.$store.state.group_id,
      }
      this._onwApi_call(methodString,params)
    },

    photos_getMarketUploadServer(main_photo = 0) { 
      let methodString = 'photos.getMarketUploadServer'
      let params = {
        'group_id' :  this.$store.state.group_id,//положительное число, обязательный параметр
        'main_photo' : main_photo, //является ли фотография обложкой товара (1 — фотография для обложки, 0 — дополнительная фотография) флаг, может принимать значения 1 или 0
        'crop_x' : 2400, //координата x для обрезки фотографии (верхний правый угол). положительное число
        'crop_y' : 2400, //координата y для обрезки фотографии (верхний правый угол). положительное число
        'crop_width' : 2400, //ширина фотографии после обрезки в px. положительное число, минимальное значение 400
      }
      return this._onwApi_call(methodString,params)
    },

    upload_file(upload_url, file_link) { 
      let methodString = 'upload_file'
      let params = {
        'upload_url' : upload_url,
        'file_link' : file_link
      }
      this._onwApi_call(methodString,params)
    },
    photos_saveMarketPhoto (photo,server,hash,crop_data,crop_hash) {
      let methodString = 'photos.saveMarketPhoto'
      let params = {
      'group_id' : this.$store.state.group_id, //идентификатор группы, для которой нужно загрузить фотографию. положительное число
      'photo' :  photo, //параметр, возвращаемый в результате загрузки фотографии на сервер. строка, обязательный параметр
      'server' :  server,//параметр, возвращаемый в результате загрузки фотографии на сервер. целое число, обязательный параметр
      'hash' : hash,//параметр, возвращаемый в результате загрузки фотографии на сервер. строка, обязательный параметр
      'crop_data' : crop_data,//параметр, возвращаемый в результате загрузки фотографии на сервер. строка
      'crop_hash' : crop_hash
      }
      this._onwApi_call(methodString,params)
    },

    upload_photoToVkGroupCommonMethod(file_link, main_photo = 0) {
      let file_uploader = new Promise(async (resolve, reject) => {
        console.log('promise function')
        let response = await this.photos_getMarketUploadServer(main_photo)
        resolve(response)
      })
      file_uploader
      .then(result => {
          let json_result = JSON.parse(result)
          return json_result.response.upload_url
        }
      ).then(url=>{
        console.log(url)
      });
    },
    _onwApi_call (method, params) {
      let url = this.ownApiServer
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
        return result.json()
        //return result.text()
      }).then((json)=>{
        this.responce_text = json
        //console.log(this.responce_text)
        return json
      })
    }
  }
}
</script>

<style scoped>

</style>
