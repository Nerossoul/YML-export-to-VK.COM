
<template>
  <div class="card mt-3">
    <div class="row mt-2">
      <div class="col-sm">
        <div class="card">
          <button
            type="button"
            class="btn btn-outline-secondary"
            @click="export_AllProductsToVk()"
          >Export all products</button>
        </div>
      </div>
    </div>
    <div class="row mt-2">
      <div class="col-sm">
        <div class="card">
          <button
            type="button"
            class="btn btn-outline-secondary"
            @click="market_search('shtatnaya-kamera-zadnego-vida-haima-m3-ccd')"
          >search 2510</button>
        </div>
      </div>
    </div>
    <br>
    <h5>{{ current_product.vendorCode }}</h5>
    {{ current_product.name}}
    <hr>
    <h5>Progress text</h5>
    <p style="white-space: pre-line">{{ action_string }}</p>
  </div>
</template>

<script>
/* eslint-disable */

export default {
    name: 'vkApi',
  data () {
    return {
      ownApiServer: 'https://playavto.ru/vk_export/server/',
      response_text: '',
      sleeping_period: 400,
      sleeping_period_text: ' ⏸️️ Pause end. (ms)-',
      prepared_product_data: {},
      current_product: {},
      action_string: '',
      action_string_separator: `
      `
    }
  },
  methods: {

    market_get (count, offset) {
      let methodString = 'market.get'
      let params = {
        owner_id: '-'+this.$store.state.group_id,
        album_id: 0,
        count: count,
        offset: offset,
        extended: 0        
      }
      return this._onwApi_call(methodString,params)
    },

    market_getAlbums () {
      let methodString = 'market.getAlbums'
      let params = {
        owner_id: '-'+this.$store.state.group_id,
      }
      return this._onwApi_call(methodString,params)
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

    update_PhotoBase(link, photo_id) {
      let methodString = 'update_potobase'
      let params = {
       'link' : link,
       'photo_id' : photo_id
      }
      return this._onwApi_call(methodString,params)
    },

    market_search(model_id) {      
      let methodString = 'market.search'
      let params = {
        'owner_id' : '-' + this.$store.state.group_id, //идентификатор сообщества, которому принадлежат товары. целое число, обязательный параметр
        'album_id' : 0, //идентификатор подборки, товары из которой нужно вернуть. положительное число, по умолчанию 0
        'q' : model_id, //строка поискового запроса. Например, зеленые тапочки. строка
        // 'q' : '#' +  model_id, //строка поискового запроса. Например, зеленые тапочки. строка
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
      let methodString = 'market.edit'
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
    market_delete (product_id) {
      let methodString = 'market.delete'
      let params = {
        'owner_id'      : '-' + this.$store.state.group_id,
		    'item_id' : product_id 
      }
      return this._onwApi_call(methodString,params)
    },

    market_add(name,description,price,main_photo_id,photo_ids) {
      let methodString = 'market.add'
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
    
    market_addToAlbum(market_item_id, album_id) {

      let methodString = 'market.addToAlbum'
      let params = {
        'owner_id' : '-' + this.$store.state.group_id,
        'item_id'  : market_item_id,
        'album_ids' : album_id
      }
      return this._onwApi_call(methodString,params)
    },

    get_all_product_ids () {
      return new Promise(async (resolve, reject) => {
        let counter = Array.from(Array(75).keys());
        let count = 200
        let offset = 0
        let all_product_ids = []
        for (let i in counter) {
          let market_products = await this.market_get (count, offset)
          // console.log(market_products.response.items.length)
          // console.log(market_products.response.items)
          await this.pause(this.sleeping_period)
          for (let product in market_products.response.items) {
            all_product_ids.push(market_products.response.items[product].id)
          }
          offset += market_products.response.items.length
          if (market_products.response.items.length< 200) {
            break
          } 
        }
        resolve(all_product_ids)
      })
      .then(result=>{
        return result
      })
    },

    isProductInMarket(search_string) {
      // console.log('-Is ' + search_string + ' in market?')
      return new Promise(async (resolve, reject) => {
        let searchResult = await this.market_search(search_string) 
        resolve(searchResult)
      })
      .then(x => new Promise(resolve => setTimeout(() => {
        // console.log(this.sleeping_period_text + this.sleeping_period)
        resolve(x)
      }, this.sleeping_period)))
      .then(searchResult=>{
        if (searchResult.error) {
          console.error('Search in market ERROR')
          console.error(searchResult)
        }
        if (searchResult.response.count == 1) {
          console.log('Is ' + search_string + ' in market? - YES')
          this.logCurrentAction('Есть в группе. Запуск процесса обновления' + this.action_string_separator)
          return searchResult.response.items[0].id
        } else if (searchResult.response.count > 1) {
          // console.log('PRODUCTS DUPLACATED!!!! COUNT: ' + searchResult.response.count + ' of ' + search_string)
          for (let item_index in searchResult.response.items) {
            console.log(searchResult.response.items[item_index].description.indexOf(search_string))
            if (searchResult.response.items[item_index].description.indexOf(search_string) != -1) {
              console.error('we take ' + item_index)
              return searchResult.response.items[item_index].id
            }
          }
          return false
        } else {
          console.log('Is ' + search_string + ' in market? - NO')
          this.logCurrentAction('Нет в группе. Запуск процесса добавления' + this.action_string_separator)
          return false
        }
      })

    },
    export_AllProductsToVk() {
      let products = this.$store.state.products
      let productCounter = 0
      let market_item_ids = []
      return new Promise(async (resolve, reject) => {
        for (let product of products) {
          this.current_product = product
          console.log(productCounter, product.vendorCode);
          //search products in market by product alias
          let url_parts = product.url.split('/')
          let search_string = url_parts[url_parts.length-1]
          if (search_string == '') {
            search_string = url_parts[url_parts.length-2]
          }
          let market_item_id = await this.isProductInMarket(search_string)
          let productPrepariedData = await this.prepare_ProductData(productCounter)
          console.log('Product data preparied. See below')
          console.log(productPrepariedData)
          let name = productPrepariedData.name
          let description = productPrepariedData.description
          let price = productPrepariedData.price
          let main_photo_id = productPrepariedData.main_photo_id
          let photo_ids = productPrepariedData.photo_ids
          if (market_item_id != false) {
            market_item_ids.push(market_item_id)
            console.log( 'UPDATE PRODUCT! ' + market_item_id )
            this.logCurrentAction('Отправляем запрос на изменение товара' + this.action_string_separator)
            let response = await this.market_edit(market_item_id,name,description,price,main_photo_id,photo_ids)
            if (response.response == 1) {
              console.log('Product updated')
              this.logCurrentAction('Успешно!' + this.action_string_separator)
            } else {
              console.log('Error update')
              this.logCurrentAction('Oшибка: товар не обновлен!' + this.action_string_separator)
            }

            await this.pause(this.sleeping_period)
            let AlbumId = this.$store.state.CategoryToAlbumId[product.categoryId]
            console.log('Add this product to album')
            this.logCurrentAction('Дoбавляем данный товар в подбоку' + this.action_string_separator)
            let add_to_album_result = await this.market_addToAlbum(market_item_id, AlbumId)
            if (add_to_album_result.response == 1) {
              console.log('Product added to album')
              this.logCurrentAction('Успешно добавлен в подборку!' + this.action_string_separator)
            } else if (add_to_album_result.error.error_code == 1404){
              console.log('Already added to this album')
              this.logCurrentAction('Товар уже добавлен в эту подборку' + this.action_string_separator)
            } else {
              console.log('Error add to album')
              this.logCurrentAction('Oшибка: товар не добавлен в подборку!' + this.action_string_separator)
            }

          } else {
            console.log('ADD PRODUCT!')
            this.logCurrentAction('Отправляем запрос на добавление товара' + this.action_string_separator)
            let response = await this.market_add(name,description,price,main_photo_id,photo_ids)
            try {
              if (response.error) {

                // TODO create error handler function

                console.log('ERROR')
                console.log('error_code: ' + response.error.error_code)
                console.error('error_msg: ' + response.error.error_msg)
                if (response.error.error_msg == 'One of the parameters specified was missing or invalid: photo not found or already assigned to another item') {
                console.log(response.error.request_params[8].value)
                console.log(response.error.request_params[9].value)
                console.log('you must to delete photos from base. Start fixin this mistake')
                let bad_id1 = response.error.request_params[8].value
                let bad_id2 = response.error.request_params[9].value

                this.$store.state.badPhotoIds = ((bad_id1+','+bad_id2).split(','))
                console.log(this.$store.state.badPhotoIds)
                productPrepariedData = await this.prepare_ProductData(productCounter)
                console.log('Product data preparied. See below')
                console.log(productPrepariedData)
                let name = productPrepariedData.name
                let description = productPrepariedData.description
                let price = productPrepariedData.price
                let main_photo_id = productPrepariedData.main_photo_id
                let photo_ids = productPrepariedData.photo_ids
                response = await this.market_add(name,description,price,main_photo_id,photo_ids)
                }
              } 
              if (response.error) {

                // TODO create error handler function

                console.log('ERROR')
                console.log('error_code: ' + response.error.error_msg)
              }
              if (response.response.market_item_id) {
                market_item_ids.push(response.response.market_item_id)
                console.log('Product added')
                this.logCurrentAction('Успешно!' + this.action_string_separator)
                await this.pause(this.sleeping_period)
                let AlbumId = this.$store.state.CategoryToAlbumId[product.categoryId]
                let market_item_id = response.response.market_item_id
                console.log('Add this product to album')
                this.logCurrentAction('Дoбавляем данный товар в подбоку' + this.action_string_separator)
                let add_to_album_result = await this.market_addToAlbum(market_item_id, AlbumId)
                if (add_to_album_result.response == 1) {
                  console.log('Product added to album')
                  this.logCurrentAction('Успешно добавлен в подборку!' + this.action_string_separator)
                } else {
                  console.log('Error add to album')
                  this.logCurrentAction('Oшибка: товар не добавлен в подборку!' + this.action_string_separator)
                }
              } else {
                console.log('Error: Product is not added')
                this.logCurrentAction('Oшибка: товар не добавлен!' + this.action_string_separator)
              }
            } catch(error) {
              console.log('Catch Error')
              console.error(error)
              this.logCurrentAction('Критическая ошибка: ' + this.action_string_separator)
            }
            
          }
          await this.pause(this.sleeping_period)
          this.logCurrentAction() // очистить лог.
          ++productCounter
          document.title = 'VK EXPORT:( '+ productCounter +'/' +this.$store.state.products.length + ' )'
          // if (productCounter >10){
            //  break
          // } 
        }
        resolve(market_item_ids)
      })
      .then(async (market_item_ids)=> {
        console.log(market_item_ids)
        let all_product_ids = await this.get_all_product_ids()
        console.log(all_product_ids)
        let product_ids_for_delete = await this.get_bad_products_ids2(all_product_ids, market_item_ids)
        await this.logCurrentAction('Удаляем товары снятые с продажи:' + this.action_string_separator)
        for (let product in product_ids_for_delete) {
          await this.logCurrentAction()
          let product_delete_result = await this.market_delete(product_ids_for_delete[product])
          await this.pause(this.sleeping_period)
          if (product_delete_result.error) {
            await this.logCurrentAction(product_ids_for_delete[product] + ' Ошибка удаления' + this.action_string_separator)
            console.error(product_delete_result)
          } else {
            await this.logCurrentAction(product_ids_for_delete[product] + ' Удален ' + this.action_string_separator)
            console.log(product_ids_for_delete[product] + 'IS DELETED')
          }
        }
        console.log('ВЫГРУЗКА ЗАКОНЧЕНА')
        this.logCurrentAction('РАБОТА ВЫПОЛНЕНА')
      })
    },

    get_bad_products_ids2(all_product_ids, market_item_ids) {
      let difference = all_product_ids.filter(x => !market_item_ids.includes(x));
      console.log(difference)
      return difference
    },

    get_photo_id(file_link) {
      return new Promise(async (resolve, reject) => {
        let photo_id = ''
        if (this.$store.state.photoBase[file_link]) {
          photo_id = this.$store.state.photoBase[file_link]
          if (this.$store.state.badPhotoIds.includes(photo_id.toString())) {
            photo_id = ''
          }
        } 
        if (photo_id == '') {
          photo_id = await this.upload_photoToVkGroupCommonMethod(file_link)
        }
        resolve(photo_id)
      })
      .then(photo_id => {
        return photo_id
      })
    },

    prepare_ProductData(array_index) {
      return new Promise(async (resolve, reject) => {
        this.logCurrentAction('Формируем запрос' + this.action_string_separator)
        let productPhotoUrls = this.$store.state.products[array_index].picture
        let photoCounter = 0
        let main_photo_id = ''
        let photo_ids = ''
        let separator = ''
        if (Array.isArray(productPhotoUrls)) {
          for (let item of productPhotoUrls) {
          ++photoCounter
          if (photoCounter == 1) {
            main_photo_id = await this.get_photo_id(item)
          } else if (photoCounter<=5) {
            if (photo_ids != '') {separator = ','}
            photo_ids += separator + await this.get_photo_id(item)
          } else {
            console.log('break')
            break
          }
          // console.log('main_photo_id: ' + main_photo_id)
          // console.log('photo_ids: ' + photo_ids)
          }
          resolve([main_photo_id,photo_ids])
        } else {
          main_photo_id = await this.get_photo_id(productPhotoUrls)
          resolve([main_photo_id,''])
        }
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
          Наличие на складе: ` + onStore + `
          Ссылка на сайт: ` + productSiteUrl + '?utm_source=vk.com&utm_medium=group_shop&utm_campaign=vk.com_group_shop&utm_content=product_'+ vendorCode +`
          ОПИСАНИЕ ТОВАРА:
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
        this.logCurrentAction('Запрос сформирован' + this.action_string_separator)
        return data
      })
    },

    upload_photoToVkGroupCommonMethod(file_link) {
      console.log('----------------------------------------')
      console.log('Uploading new photo')
      this.logCurrentAction('Loading photo: ')
      return new Promise(async (resolve, reject) => {
        console.log('Getting Upload Server')
        // TODO make reject scenario
        let response = await this.photos_getMarketUploadServer()
        this.logCurrentAction('✓')
        resolve(response)
      })
      .then(x => new Promise(resolve => setTimeout(() => {
        // console.log(this.sleeping_period_text + this.sleeping_period)
        resolve(x)
        }, this.sleeping_period)))
      .then(async (result) => {
        // TODO make reject scenario
        console.log('Uploading photo to server')
        this.logCurrentAction('✓')
        let response = await this.upload_file(result.response.upload_url, file_link)
        return response
      })
      .then(x => new Promise(resolve => setTimeout(() => {
        // console.log(this.sleeping_period_text + this.sleeping_period)
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
        this.logCurrentAction('✓')
        let response = await this.photos_saveMarketPhoto(photo,server,hash,crop_data,crop_hash)
        return response
      })
      .then(x => new Promise(resolve => setTimeout(() => {
        // console.log(this.sleeping_period_text + this.sleeping_period)
        resolve(x)
        }, this.sleeping_period)))
      .then(saved_photo_info=>{
        let photo_id = saved_photo_info.response[0].id
        this.logCurrentAction('OK' + this.action_string_separator)
        this.update_PhotoBase(file_link, photo_id) 
        return photo_id
      })
      .catch(async (error)=>{
        console.log('^^^^^^^^^^^^^^^')
        console.error(error)
        console.log('ERROR UPLOADING PHOTO: Skip this link')
        this.logCurrentAction(this.action_string_separator + 'Ошибка загрузки фото. Пропускаем данную ссылку.' + this.action_string_separator)
        // let response = await this.upload_photoToVkGroupCommonMethod(file_link)
        return ''
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
        // return result.text()
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
      .catch(async (error)=>{
        console.log('^^^^^^^^^^^^^^^')
        console.log(error)
        console.log('ERROR API CALL: RETRY')
        this.logCurrentAction('Ошибка API запроса. Пробуем ещё раз.' + this.action_string_separator)
        let response = await this._onwApi_call (method, params)
        return response
      })
    },

    logCurrentAction(action_string) {
      this.action_string += action_string
      if (arguments.length == 0) {
        this.action_string = ''
      }
    },

    pause(pausePeriod) {
      return new Promise(async (resolve, reject) => {
        // console.log('Pause ' + pausePeriod + ' ms began')
        setTimeout(()=>resolve('Pause end'), pausePeriod)
      })
      .then(pauseEndMessage=>{
        // console.log(pauseEndMessage)
        return pauseEndMessage
      })
    },
  }
}
</script>

<style scoped>
</style>
