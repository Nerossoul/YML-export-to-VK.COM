
<template>
  <div class="card mt-3">
      <h2>{{ msg }}</h2>
      <div class="row mt-2">
        <div class="col-sm">
          <div class="card">
            <button type="button" class="btn btn-outline-secondary" @click="prepare_ProductData(500) ">Подготовить товар</button>
          </div>
        </div>
        <div class="col-sm">
          <div class="card">
            <button type="button" class="btn btn-outline-secondary"  @click="upload_photoToVkGroupCommonMethod('https://playavto.ru/image/import_files/91/91ceb3bd-437a-11e8-bd9c-408d5c3aa21e_91ceb3bf-437a-11e8-bd9c-408d5c3aa21e.jpeg', 1) ">common file upload</button>
          </div>
        </div>
        <div class="col-sm">
          <div class="card">
            <button type="button" class="btn btn-outline-secondary"  @click="isProductInMarket('000000021307')">Is product in market</button>
          </div>
        </div>
      </div>
      <br>
      {{ response_text }}
      <hr>
       <div v-for="(value, key) in prepared_product_data" :key="value.id" class="border border-primary mt-1"><h4>{{key}}</h4>{{value}}</div>
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
      response_text: '',
      sleeping_period: 400,
      prepared_product_data: {}
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

    photos_getMarketUploadServer() { 
      let methodString = 'photos.getMarketUploadServer'
      let params = {
        'group_id' :  this.$store.state.group_id,//положительное число, обязательный параметр
        'main_photo' : 1, //является ли фотография обложкой товара (1 — фотография для обложки, 0 — дополнительная фотография) флаг, может принимать значения 1 или 0
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

    isProductInMarket(model_id) {
      console.log('-Is ' + model_id + ' in market?')
      return new Promise(async (resolve, reject) => {
        let searchResult = await this.market_search(model_id) 
        resolve(searchResult)
      }).then(searchResult=>{
        if (searchResult.response.count > 0) {
      console.log('-YES! The ' + model_id + ' is in market')
          return true
        } else {
      console.log('-NO! The ' + model_id + ' isn\'t in market')
          return false
        }
      })

    },
    export_AllProductsToVk() {

    },

    prepare_ProductData(array_index) {
      return new Promise(async (resolve, reject) => {
        let productPhotoUrls = this.$store.state.products[array_index].picture
        let photoCounter = 0
        let main_photo_id = ''
        let photo_ids = ''
        let separator = ''
        for (let item of productPhotoUrls) {
          ++photoCounter
          if (photoCounter == 1) {
            main_photo_id = await this.upload_photoToVkGroupCommonMethod(item)
          } else if (photoCounter<=5) {
            if (photo_ids != '') {separator = ','}
            photo_ids += separator + await this.upload_photoToVkGroupCommonMethod(item)
          } else {
            console.log('break')
            break
          }
          console.log('main_photo_id: ' + main_photo_id)
          console.log('photo_ids: ' + photo_ids)
        }
        resolve([main_photo_id,photo_ids])
      })
      .then((photo_id_array) => {
        this.prepared_product_data = this.$store.state.products[array_index]
        let onStore = 'НЕТ. Товар в пути. НАПИШИТЕ ПРОДАВЦУ что бы узнать дату поступления на склад.'
        if (this.prepared_product_data._available) {
          onStore = 'ДА'
        }
        this.prepared_product_data._available
        let productSiteUrl = this.prepared_product_data.url
        let vendorCode = this.prepared_product_data.vendorCode
        let description = this.prepared_product_data.description
        description = description.replace( /\s{2,}/g, " " )
        description = 'Артикул товара: #' + vendorCode + ` 
          %0A Наличие на складе: ` + onStore + `
          %0A Ссылка на сайт: ` + productSiteUrl + '?utm_source=vk.com&utm_medium=group_shop&utm_campaign=vk.com_group_shop&utm_content=product_'+ vendorCode +`
          %0A ОПИСАНИЕ ТОВАРА:
          `+ description
        let data = {
          'name'          : this.prepared_product_data.name,
          'description'   : description,
          'category_id'   : 205,
          'price'         : this.prepared_product_data.price,
          'deleted'       : 0,
          'main_photo_id' : photo_id_array[0], 
          'photo_ids'     : photo_id_array[1],
        }
        this.prepared_product_data = data
        return data
      })
    },

    upload_photoToVkGroupCommonMethod(file_link) {
      console.log('----------------------------------------')
      console.log('Uploading new photo')
      return new Promise(async (resolve, reject) => {
        console.log('Getting Upload Server')
        // TODO make reject scenario
        let response = await this.photos_getMarketUploadServer()
        resolve(response)
      })
      .then(x => new Promise(resolve => setTimeout(() => {
        console.log('pause: '+ this.sleeping_period + " ms has been ended")
        resolve(x)
        }, this.sleeping_period)))
      .then(async (result) => {
        // TODO make reject scenario
        console.log('Uploading photo to server')
        let response = await this.upload_file(result.response.upload_url, file_link)
        return response
      })
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
        // TODO make reject scenario
        console.log('Saving uploaded photo')
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
      }).then((json)=>{
        params.access_token = 'hidden'
        this.response_text = {
          method : method,
          params : params,
          response: json
        }
        console.log(this.response_text)
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
