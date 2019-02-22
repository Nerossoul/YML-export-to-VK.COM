
<template>
  <div class="card mt-3">
      <h2>{{ msg }}</h2>
      <div class="row mt-2">
        <div class="col-sm">
          <div class="card">
            <button type="button" class="btn btn-outline-secondary" @click="market_search('00000002043')">Search</button>
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
      responce_text: '',
      sleeping_period: 1000
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
      return this._onwApi_call(methodString,params)
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
      return this._onwApi_call(methodString,params)
    },

    upload_photoToVkGroupCommonMethod(file_link, main_photo = 0) {
      let file_uploader = new Promise(async (resolve, reject) => {
        let response = await this.photos_getMarketUploadServer(main_photo)
        resolve(response)
      })
      file_uploader
      .then(x => new Promise(resolve => setTimeout(() => {
        console.log('pause: '+ this.sleeping_period + " ms has been ended")
        resolve(x)
        }, this.sleeping_period)))
      .then(async (result) => {
          let response = await this.upload_file(result.response.upload_url, file_link)
          return response
        }
      )
      .then(x => new Promise(resolve => setTimeout(() => {
        console.log('pause: '+ this.sleeping_period + " ms has been ended")
        resolve(x)
        }, this.sleeping_period)))
      .then(async (uploaded_file_info) => {
        let photo = uploaded_file_info.photo
        let server =uploaded_file_info.server
        let hash =uploaded_file_info.hash
        let crop_data =uploaded_file_info.crop_data
        let crop_hash =uploaded_file_info.crop_hash
        let response = await this.photos_saveMarketPhoto(photo,server,hash,crop_data,crop_hash)
        return response
      })
      .then(x => new Promise(resolve => setTimeout(() => {
        console.log('pause: '+ this.sleeping_period + " ms has been ended")
        resolve(x)
        }, this.sleeping_period)))
      .then(saved_photo_info=>{
        let photo_id = saved_photo_info.response[0].id
        return photo_id
      })
    },

    market_search(model_id) {
      
      let methodString = 'market.search'
      let params = {
        'owner_id' : '-' + this.$store.state.group_id, //идентификатор сообщества, которому принадлежат товары. целое число, обязательный параметр
        'album_id' : 0, //идентификатор подборки, товары из которой нужно вернуть. положительное число, по умолчанию 0
        'q' : '#' +  model_id, //строка поискового запроса. Например, зеленые тапочки. строка
        'price_from' : 0, //минимальное значение цены товаров в сотых долях единицы валюты. Например, 100000. положительное число
        'price_to' : 1000000000, //максимальное значение цены товаров в сотых долях единицы валюты. Например, 1410000. положительное число
        'sort' : 3,//вид сортировки. 0 — пользовательская расстановка, 1 — по дате добавления товара, 2 — по цене, 3 — по популярности. целое число, по умолчанию 0
        'rev0' : 0, //— не использовать обратный порядок, 1 — использовать обратный порядок. положительное число, по умолчанию 1
        'offset' : 0,//смещение относительно первого найденного товара для выборки определенного подмножества. положительное число
        'count' : 200, //количест
      }
      return this._onwApi_call(methodString,params)
    },

    market_edit(item_id,name,description,price,main_photo_id,photo_ids) {
      let methodString = 'market.search'
      let params = {
      'owner_id'      : '-' + this.$store.state.group_id,
      'item_id'       : item_id,
      'name'          : name,
      'description'   : description,
      'category_id'   : 205,
      'price'         : price,
      'deleted'       : 0,
      'main_photo_id' : main_photo_id, 
      'photo_ids'     : photo_ids,
      }
      return this._onwApi_call(methodString,params)
    },

    market_add(name,description,price,main_photo_id,photo_ids) {
      let methodString = 'market.search'
      let params = {
        'owner_id'      : '-' + this.$store.state.group_id,
        'name'          : name,
        'description'   : description,
        'category_id'   : 205,
        'price'         : price,
        'deleted'       : 0,
        'main_photo_id' : main_photo_id, 
        'photo_ids'     : photo_ids,
      }
      return this._onwApi_call(methodString,params)
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
        this.responce_text = {
          method : method,
          response: json
        }
        console.log(this.responce_text)
        if (typeof json == 'object') {
          return json
        } else {
          return JSON.parse(json)
        }
      })
    }
  }
}
</script>

<style scoped>

</style>
