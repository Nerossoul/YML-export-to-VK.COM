<template>
  <div class="card mt-3">
    <div class="row mt-2" v-if="show_export_button">
      <div class="col-sm m-3">
        <div class="card">
          <button
            type="button"
            class="btn btn-dark"
            @click="export_AllProductsToVk()"
          >Export all products
          </button><br>
          <button v-if="isShowContinueButton()"
            type="button"
            class="btn btn-dark"
            @click="export_AllProductsToVk(true)"
          >Export products from {{lastExportedProductNumber}} number of {{$store.state.products.length}}
          </button>
        </div>
      </div>
    </div>
    <div class="row mt-2" v-else>
      <div class="col-sm m-3">
        <div class="card">
          <h5 class="mt-3 pl-3">{{ current_product.vendorCode }}</h5>
          <span class="pl-3">{{ current_product.name }}</span>
          <div v-if="isCaptchaNecessary">
            <h4>Требуется ввести каптчу</h4>
            <img :src="captchaImg" alt="" border="1">
            <input type="text" placeholder="Введите текст с картинки" v-model="captchaUserInputKey">
            <button @click="sendCaptcha()">Go</button>
          </div>
          <hr/>
          <h5 class="pl-3">Progress text</h5>
          <p class="pl-3" style="white-space: pre-line">{{ action_string }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'vkApi',
  data () {
    return {
      show_export_button: true,
      ownApiServer: process.env.OWN_API_SERVER,
      response_text: '',
      sleeping_period: 400,
      sleeping_period_text: '⏸️️ Pause end. (ms)-',
      prepared_product_data: {},
      current_product: {},
      current_product_number: 0,
      progress_bar_per_cent: 0,
      action_string: '',
      action_string_separator: `
  `,
      isCaptchaNecessary: false,
      captchaImg: 'https:\\/\\/api.vk.com\\/captcha.php?sid=580204246684&s=1',
      captchaUserInputKey: '',

      lastExportedProductNumber: 0
    }
  },
  computed: {
    progress_bar () {
      return this.get_prog_bar_string('⣀⣄⣤⣦⣶⣷⣿', this.progress_bar_per_cent, 10, '')
    }
  },
  methods: {
    consoleLog () {
      if (process.env.NODE_ENV === 'development') {
        console.log(...(Array.from(arguments)))
      }
    },
    isShowContinueButton () {
      return ((parseInt(this.lastExportedProductNumber)) > 0) && ((parseInt(this.lastExportedProductNumber) <= this.$store.state.products.length))
    },
    market_get (count, offset) {
      let methodString = 'market.get'
      let params = {
        owner_id: '-' + this.$store.state.group_id,
        album_id: 0,
        count: count,
        offset: offset,
        extended: 0
      }
      return this._onwApi_call(methodString, params)
    },

    market_getAlbums () {
      let methodString = 'market.getAlbums'
      let params = {
        owner_id: '-' + this.$store.state.group_id
      }
      return this._onwApi_call(methodString, params)
    },

    photos_getMarketUploadServer () {
      let methodString = 'photos.getMarketUploadServer'
      let params = {
        'group_id': this.$store.state.group_id, // положительное число, обязательный параметр
        'main_photo': 1, // является ли фотография обложкой товара (1 — фотография для обложки, 0 — дополнительная фотография) флаг, может принимать значения 1 или 0
        'crop_x': 2400, // координата x для обрезки фотографии (верхний правый угол). положительное число
        'crop_y': 2400, // координата y для обрезки фотографии (верхний правый угол). положительное число
        'crop_width': 2400 // ширина фотографии после обрезки в px. положительное число, минимальное значение 400
      }
      return this._onwApi_call(methodString, params)
    },

    upload_file (uploadUrl, fileLink) {
      let methodString = 'upload_file'
      let params = {
        'upload_url': uploadUrl,
        'file_link': fileLink
      }
      return this._onwApi_call(methodString, params)
    },

    photos_saveMarketPhoto (photo, server, hash, cropData, cropHash) {
      let methodString = 'photos.saveMarketPhoto'
      let params = {
        'group_id': this.$store.state.group_id, // идентификатор группы, для которой нужно загрузить фотографию. положительное число
        'photo': photo, // параметр, возвращаемый в результате загрузки фотографии на сервер. строка, обязательный параметр
        'server': server, // параметр, возвращаемый в результате загрузки фотографии на сервер. целое число, обязательный параметр
        'hash': hash, // параметр, возвращаемый в результате загрузки фотографии на сервер. строка, обязательный параметр
        'crop_data': cropData, // параметр, возвращаемый в результате загрузки фотографии на сервер. строка
        'crop_hash': cropHash
      }
      return this._onwApi_call(methodString, params)
    },

    update_PhotoBase (link, photoId, productVendorCode) {
      let methodString = 'update_photobase'
      let params = {
        'link': link,
        'photo_id': photoId,
        'productVendorCode': productVendorCode
      }
      return this._onwApi_call(methodString, params)
    },

    market_search (modelId) {
      let methodString = 'market.search'
      let params = {
        'owner_id': '-' + this.$store.state.group_id, // идентификатор сообщества, которому принадлежат товары. целое число, обязательный параметр
        'album_id': 0, // идентификатор подборки, товары из которой нужно вернуть. положительное число, по умолчанию 0
        'q': modelId, // строка поискового запроса. Например, зеленые тапочки. строка
        'price_from': 0, // минимальное значение цены товаров в сотых долях единицы валюты. Например, 100000. положительное число
        'price_to': 1000000000, // максимальное значение цены товаров в сотых долях единицы валюты. Например, 1410000. положительное число
        'sort': 3, // вид сортировки. 0 — пользовательская расстановка, 1 — по дате добавления товара, 2 — по цене, 3 — по популярности. целое число, по умолчанию 0
        'rev0': 0, // — не использовать обратный порядок, 1 — использовать обратный порядок. положительное число, по умолчанию 1
        'offset': 0, // смещение относительно первого найденного товара для выборки определенного подмножества. положительное число
        'count': 200 // количест
      }
      return this._onwApi_call(methodString, params)
    },

    market_edit (itemId, name, description, price, mainPhotoId, photoIds) {
      let methodString = 'market.edit'
      let params = {
        'owner_id': '-' + this.$store.state.group_id,
        'item_id': itemId,
        'name': name,
        'description': description,
        'category_id': 205,
        'price': price,
        'deleted': 0,
        'main_photo_id': mainPhotoId,
        'photo_ids': photoIds
      }
      return this._onwApi_call(methodString, params)
    },

    market_delete (productId) {
      let methodString = 'market.delete'
      let params = {
        'owner_id': '-' + this.$store.state.group_id,
        'item_id': productId
      }
      return this._onwApi_call(methodString, params)
    },

    market_add (name, description, price, mainPhotoId, photoIds) {
      let methodString = 'market.add'
      let params = {
        'owner_id': '-' + this.$store.state.group_id,
        'name': name,
        'description': description,
        'category_id': 205,
        'price': price,
        'deleted': 0,
        'main_photo_id': mainPhotoId,
        'photo_ids': photoIds
      }
      return this._onwApi_call(methodString, params)
    },

    market_addToAlbum (marketItemId, albumId) {
      let methodString = 'market.addToAlbum'
      let params = {
        'owner_id': '-' + this.$store.state.group_id,
        'item_id': marketItemId,
        'album_ids': albumId
      }
      return this._onwApi_call(methodString, params)
    },

    get_all_product_ids () {
      return new Promise(async (resolve) => {
        let counter = Array.from(Array(75).keys())
        let count = 200
        let offset = 0
        let allProductIds = []
        for (let i in counter) {
          this.consoleLog('--------->', i)
          let marketProducts = await this.market_get(count, offset)
          await this.pause(this.sleeping_period)
          for (let product in marketProducts.response.items) {
            allProductIds.push(marketProducts.response.items[product].id)
          }
          offset += marketProducts.response.items.length
          if (marketProducts.response.items.length < 200) {
            break
          }
        }
        resolve(allProductIds)
      })
        .then(result => {
          return result
        })
    },

    isProductInMarket (searchString) {
      this.consoleLog('-Is ' + searchString + ' in market?')
      return new Promise(async (resolve) => {
        let searchResult = await this.market_search(searchString)
        this.consoleLog(searchResult)
        this.consoleLog('^^^^^^^^^^^^')
        resolve(searchResult)
      })
        .then(x => new Promise(resolve => setTimeout(() => {
          // this.consoleLog(this.sleeping_period_text + this.sleeping_period)
          resolve(x)
        }, this.sleeping_period)))
        .then(searchResult => {
          if (searchResult.error) {
            console.error('Search in market ERROR')
            console.error(searchResult)
          }
          if (searchResult.response.count === 1) {
            if (searchResult.response.items[0].description.indexOf(searchString) !== -1) {
              this.consoleLog('Is ' + searchString + ' in market? - YES')
              this.logCurrentAction('Есть в группе. Запуск процесса обновления' + this.action_string_separator)
              return searchResult.response.items[0].id
            }
            return false
          } else if (searchResult.response.count > 1) {
            for (let itemIndex in searchResult.response.items) {
              this.consoleLog(searchResult.response.items[itemIndex].description.indexOf(searchString))
              if (searchResult.response.items[itemIndex].description.indexOf(searchString) !== -1) {
                this.consoleLog('Есть в группе. Запуск процесса обновления')
                this.logCurrentAction('Есть в группе. Запуск процесса обновления' + this.action_string_separator)
                return searchResult.response.items[itemIndex].id
              }
            }
            return false
          } else {
            this.consoleLog('Is ' + searchString + ' in market? - NO')
            this.logCurrentAction('Нет в группе. Запуск процесса добавления' + this.action_string_separator)
            return false
          }
        })
    },
    export_AllProductsToVk (useLastExportedProductNumber) {
      this.show_export_button = false
      let products = this.$store.state.products
      let productCounter = 0
      if (useLastExportedProductNumber === true) {
        productCounter = parseInt(this.lastExportedProductNumber)
      }
      let marketItemIds = []
      return new Promise(async (resolve) => {
        // Skip exported products
        let localCounter = -1
        for (let product of products) {
          localCounter += 1
          if (localCounter !== productCounter) {
            continue
          }

          this.current_product_number = productCounter + 1
          this.change_title(0)
          this.current_product = product
          this.consoleLog(productCounter, product.vendorCode)
          const searchString = '[' + product.vendorCode + ']'
          let marketItemId = await this.isProductInMarket(searchString)
          this.change_title(8)
          let productPreparedData = await this.prepare_ProductData(productCounter)
          this.change_title(64)
          this.consoleLog('Product data prepared. See below')
          this.consoleLog(productPreparedData)
          if (productPreparedData.main_photo_id === '') { // no photos skip product;
            continue
          }
          let name = productPreparedData.name
          let description = productPreparedData.description
          let price = productPreparedData.price
          let mainPhotoId = productPreparedData.main_photo_id
          let photoIds = productPreparedData.photo_ids
          if (marketItemId !== false) {
            marketItemIds.push(marketItemId)
            this.consoleLog('UPDATE PRODUCT! ' + marketItemId)
            this.logCurrentAction('Отправляем запрос на изменение товара' + this.action_string_separator)
            let response = await this.market_edit(marketItemId, name, description, price, mainPhotoId, photoIds)
            if (response.response === 1) {
              this.consoleLog('Product updated')
              this.logCurrentAction('Успешно!' + this.action_string_separator)
            } else {
              this.consoleLog('ERROR')
              this.consoleLog('error_code: ' + response.error.error_code)
              console.error('error_msg: ' + response.error.error_msg)
              if (response.error.error_msg === 'One of the parameters specified was missing or invalid: photo not found or already assigned to another item') {
                this.consoleLog(response.error.request_params[8].value)
                this.consoleLog(response.error.request_params[9].value)
                this.consoleLog('you must to delete photos from base. Start fixin this mistake')
                let badId1 = response.error.request_params[9].value
                let badId2 = response.error.request_params[10].value

                this.$store.state.badPhotoIds = ((badId1 + ',' + badId2).split(','))
                this.consoleLog(this.$store.state.badPhotoIds)
                this.change_title(8)
                productPreparedData = await this.prepare_ProductData(productCounter)
                this.consoleLog('Product data prepared. See below')
                this.consoleLog(productPreparedData)
                if (productPreparedData.main_photo_id === '') { // no photos skip product;
                  continue
                }
                let name = productPreparedData.name
                let description = productPreparedData.description
                let price = productPreparedData.price
                let mainPhotoId = productPreparedData.main_photo_id
                let photoIds = productPreparedData.photo_ids
                response = await this.market_edit(marketItemId, name, description, price, mainPhotoId, photoIds)
              }
              if (response.error) {
                console.error(`
            ПОВТОРНАЯ ОШИБКА
            error_msg: ` + response.error.error_msg)
              }
            }
            this.change_title(85)
            await this.pause(this.sleeping_period)
            let AlbumId = this.$store.state.CategoryToAlbumId[product.categoryId]
            this.consoleLog('Add this product to album')
            this.logCurrentAction('Дoбавляем данный товар в подбоку' + this.action_string_separator)
            let addToAlbumResult = await this.market_addToAlbum(marketItemId, AlbumId)
            if (addToAlbumResult.response === 1) {
              this.consoleLog('Product added to album')
              this.logCurrentAction('Успешно добавлен в подборку!' + this.action_string_separator)
            } else if (addToAlbumResult.error.error_code === 1404) {
              this.consoleLog('Already added to this album')
              this.logCurrentAction('Товар уже добавлен в эту подборку' + this.action_string_separator)
            } else {
              this.consoleLog('Error add to album')
              this.logCurrentAction('Oшибка: товар не добавлен в подборку!' + this.action_string_separator)
            }
            this.change_title(100)
          } else {
            this.consoleLog('ADD PRODUCT!')
            this.logCurrentAction('Отправляем запрос на добавление товара' + this.action_string_separator)
            let response = await this.market_add(name, description, price, mainPhotoId, photoIds)
            this.change_title(80)
            try {
              if (response.error) {
                // TODO create error handler function
                this.consoleLog('ERROR')
                this.consoleLog('error_code: ' + response.error.error_code)
                console.error('error_msg: ' + response.error.error_msg)
                if (response.error.error_msg === 'One of the parameters specified was missing or invalid: photo not found or already assigned to another item') {
                  this.consoleLog(response.error.request_params[8].value)
                  this.consoleLog(response.error.request_params[9].value)
                  this.consoleLog('you must to delete photos from base. Start fixin this mistake')
                  let badId1 = response.error.request_params[8].value
                  let badId2 = response.error.request_params[9].value

                  this.$store.state.badPhotoIds = ((badId1 + ',' + badId2).split(','))
                  this.consoleLog(this.$store.state.badPhotoIds)
                  this.change_title(8)
                  productPreparedData = await this.prepare_ProductData(productCounter)
                  this.consoleLog('Product data prepared. See below')
                  this.consoleLog(productPreparedData)
                  if (productPreparedData.main_photo_id === '') { // no photos skip product;
                    continue
                  }
                  let name = productPreparedData.name
                  let description = productPreparedData.description
                  let price = productPreparedData.price
                  let mainPhotoId = productPreparedData.main_photo_id
                  let photoIds = productPreparedData.photo_ids
                  response = await this.market_add(name, description, price, mainPhotoId, photoIds)
                }
              }

              if (response.error) {
                // TODO create error handler function
                this.consoleLog('ERROR')
                this.consoleLog('error_code: ' + response.error.error_msg)
              }

              if (response.response.market_item_id) {
                marketItemIds.push(response.response.market_item_id)
                this.consoleLog('Product added')
                this.logCurrentAction('Успешно!' + this.action_string_separator)
                await this.pause(this.sleeping_period)
                let AlbumId = this.$store.state.CategoryToAlbumId[product.categoryId]
                let marketItemId = response.response.market_item_id
                this.consoleLog('Add this product to album')
                this.logCurrentAction('Дoбавляем данный товар в подбоку' + this.action_string_separator)
                let addToAlbumResult = await this.market_addToAlbum(marketItemId, AlbumId)
                if (addToAlbumResult.response === 1) {
                  this.consoleLog('Product added to album')
                  this.logCurrentAction('Успешно добавлен в подборку!' + this.action_string_separator)
                } else {
                  this.consoleLog('Error add to album')
                  this.logCurrentAction('Oшибка: товар не добавлен в подборку!' + this.action_string_separator)
                }
              } else {
                console.error('Error: Product is not added')
                this.logCurrentAction('Oшибка: товар не добавлен!' + this.action_string_separator)
              }
            } catch (error) {
              this.consoleLog('Catch Error')
              console.error(error)
              this.logCurrentAction('Критическая ошибка: ' + this.action_string_separator)
            }
          }
          this.change_title(100)
          if (productCounter > 10000000000) {
            await this.pause(1000000000)
          } else {
            await this.pause(this.sleeping_period)
          }
          this.logCurrentAction() // очистить лог.
          this.saveLastExportedProductNumber(productCounter)
          productCounter += 1
        }
        resolve(marketItemIds)
      })
        .then(async (marketItemIds) => {
          // do not clear market if export was not full
          if (marketItemIds.length !== this.$store.state.products.length) {
            await this.logCurrentAction('Мы получили vk_id не всех товаров, пропускаем процесс удаления неиспользуемых товаров, Когда то тут будет реализован механизм более умной очистки пока мы просто пропускаем этот шаг' + this.action_string_separator)
          } else {
            this.consoleLog(marketItemIds)
            let allProductIds = await this.get_all_product_ids()
            this.consoleLog(allProductIds)
            let productIdsForDelete = await this.get_bad_products_ids2(allProductIds, marketItemIds)
            await this.logCurrentAction('Удаляем товары снятые с продажи:' + this.action_string_separator)
            for (let product in productIdsForDelete) {
              await this.logCurrentAction()
              let productDeleteResult = await this.market_delete(productIdsForDelete[product])
              await this.pause(this.sleeping_period)
              if (productDeleteResult.error) {
                await this.logCurrentAction(productIdsForDelete[product] + ' Ошибка удаления' + this.action_string_separator)
                console.error(productDeleteResult)
              } else {
                await this.logCurrentAction(productIdsForDelete[product] + ' Удален ' + this.action_string_separator)
                this.consoleLog(productIdsForDelete[product] + 'IS DELETED')
              }
            }
          }

          this.consoleLog('ВЫГРУЗКА ЗАКОНЧЕНА')
          this.clearLastExportedProductNumber()
          this.logCurrentAction('РАБОТА ВЫПОЛНЕНА')
        })
    },

    get_bad_products_ids2 (allProductIds, marketItemIds) {
      let difference = allProductIds.filter(x => !marketItemIds.includes(x))
      this.consoleLog(difference)
      return difference
    },

    get_photo_id (fileLink, vendorCode) {
      return new Promise(async (resolve, reject) => {
        let photoId = ''
        if (this.$store.state.photoBase[vendorCode] && this.$store.state.photoBase[vendorCode][fileLink]) {
          photoId = this.$store.state.photoBase[vendorCode][fileLink]
          if (this.$store.state.badPhotoIds.includes(photoId.toString())) {
            photoId = ''
          }
        }
        if (photoId === '') {
          photoId = await this.upload_photoToVkGroupCommonMethod(fileLink, vendorCode)
        }
        resolve(photoId)
      })
        .then(photoId => {
          return photoId
        })
    },

    prepare_ProductData (arrayIndex) {
      return new Promise(async (resolve, reject) => {
        this.logCurrentAction('Формируем запрос' + this.action_string_separator)
        let productPhotoUrls = this.$store.state.products[arrayIndex].picture
        const vendorCode = this.$store.state.products[arrayIndex].vendorCode

        if (productPhotoUrls === undefined) {
          console.error('---------------')
          console.error(productPhotoUrls)
          console.error('This product don\'t have photos, SKIP...')
          console.error('---------------')
          resolve(['', ''])
        }
        let photoCounter = 0
        let mainPhotoId = ''
        let photoIds = ''
        let separator = ''
        if (Array.isArray(productPhotoUrls)) {
          for (let item of productPhotoUrls) {
            this.progress_bar_per_cent += 11
            this.change_title(this.progress_bar_per_cent)
            ++photoCounter
            if (photoCounter === 1) {
              mainPhotoId = await this.get_photo_id(item, vendorCode)
            } else if (photoCounter <= 5) {
              if (photoIds !== '') {
                separator = ','
              }
              photoIds += separator + await this.get_photo_id(item, vendorCode)
            } else {
              this.consoleLog('break')
              break
            }
          }
          resolve([mainPhotoId, photoIds])
        } else {
          mainPhotoId = await this.get_photo_id(productPhotoUrls, vendorCode)
          resolve([mainPhotoId, ''])
        }
      })
        .then((photoIdArray) => {
          this.prepared_product_data = this.$store.state.products[arrayIndex]
          let onStore = 'НЕТ. Товар в пути. НАПИШИТЕ ПРОДАВЦУ что бы узнать дату поступления на склад.'
          if (this.prepared_product_data._available) {
            onStore = 'ДА'
          }

          let productSiteUrl = this.prepared_product_data.url
          let vendorCode = this.prepared_product_data.vendorCode
          let description = this.prepared_product_data.description
          description = description.replace(/\s{2,}/g, ' ')
          description = 'Артикул товара: [' + vendorCode + `]
      Наличие на складе: ` + onStore + `
      Ссылка на сайт: ` + productSiteUrl + '?utm_source=vk.com&utm_medium=group_shop&utm_campaign=vk.com_group_shop&utm_content=product_' + vendorCode + `
      ОПИСАНИЕ ТОВАРА:
      ` + description
          let data = {
            'name': this.getProductNameBefore100Chars(this.prepared_product_data.name),
            'description': description,
            'category_id': 205,
            'price': this.prepared_product_data.price,
            'deleted': 0,
            'main_photo_id': photoIdArray[0],
            'photo_ids': photoIdArray[1]
          }
          this.prepared_product_data = data
          this.logCurrentAction('Запрос сформирован' + this.action_string_separator)
          return data
        })
    },

    getProductNameBefore100Chars (productsName) {
      if (productsName.length < 100) {
        return productsName
      }
      let nameParts = productsName.split(' ')
      this.consoleLog('---->NAME PARTS', nameParts)
      let newName = ''
      nameParts.every((part) => {
        if ((newName + ' ' + part).length < 100) {
          newName += ' ' + part
          return true
        }
        return false
      })
      return newName
    },

    upload_photoToVkGroupCommonMethod (fileLink, vendorCode) {
      this.consoleLog('----------------------------------------')
      this.consoleLog('Uploading new photo')
      this.logCurrentAction('Loading photo: ')
      return new Promise(async (resolve, reject) => {
        this.consoleLog('Getting Upload Server')
        // TODO make reject scenario
        let response = await this.photos_getMarketUploadServer()
        this.logCurrentAction('✓')
        resolve(response)
      })
        .then(x => new Promise(resolve => setTimeout(() => {
          // this.consoleLog(this.sleeping_period_text + this.sleeping_period)
          resolve(x)
        }, this.sleeping_period)))
        .then(async (result) => {
          // TODO make reject scenario
          this.consoleLog('Uploading photo to server')
          this.logCurrentAction('✓')
          let response = await this.upload_file(result.response.upload_url, fileLink)
          return response
        })
        .then(x => new Promise(resolve => setTimeout(() => {
          // this.consoleLog(this.sleeping_period_text + this.sleeping_period)
          resolve(x)
        }, this.sleeping_period)))
        .then(async (uploadedFileInfo) => {
          let photo = uploadedFileInfo.photo
          let server = uploadedFileInfo.server
          let hash = uploadedFileInfo.hash
          let cropData = uploadedFileInfo.crop_data
          let cropHash = uploadedFileInfo.crop_hash
          // TODO make reject scenario
          this.consoleLog('Saving uploaded photo')
          this.logCurrentAction('✓')
          let response = await this.photos_saveMarketPhoto(photo, server, hash, cropData, cropHash)
          return response
        })
        .then(x => new Promise(resolve => setTimeout(() => {
          // this.consoleLog(this.sleeping_period_text + this.sleeping_period)
          resolve(x)
        }, this.sleeping_period)))
        .then(savedPhotoInfo => {
          let photoId = savedPhotoInfo.response[0].id
          this.logCurrentAction('OK' + this.action_string_separator)
          this.consoleLog('-----update_PhotoBase-----------')
          this.consoleLog(fileLink, photoId, vendorCode)

          let resultOfPhotoBaseUpdate = this.update_PhotoBase(fileLink, photoId, vendorCode)
          this.consoleLog('resultOfPhotoBaseUpdate', resultOfPhotoBaseUpdate)
          return photoId
        })
        .catch(async (error) => {
          this.consoleLog('^^^^^^^^^^^^^^^')
          console.error(error)
          this.consoleLog('ERROR UPLOADING PHOTO: Skip this link')
          this.logCurrentAction(this.action_string_separator + 'Ошибка загрузки фото. Пропускаем данную ссылку.' + this.action_string_separator)
          // let response = await this.upload_photoToVkGroupCommonMethod(file_link)
          return ''
        })
    },

    sendCaptcha () {
      this.isCaptchaNecessary = false
      this.consoleLog('capcha done')
    },

    changeCaptchaTitle () {
      document.title = document.title === '---ЖДЕМ КАПТЧУ---' ? '!!! ЖДЕМ КАПТЧУ !!!' : '---ЖДЕМ КАПТЧУ---'
    },

    isResponseHasCaptcha (response) {
      if (response.error && response.error.error_code === 14) {
        return true
      }
      return false
    },

    _onwApi_call (method, params) {
      let url = this.ownApiServer
      params.access_token = this.$store.state.access_token
      let vkApiCallObject = {
        method: method,
        params: params
      }
      return fetch(url, {
        method: 'POST',
        body: JSON.stringify(vkApiCallObject),
        mode: 'cors',
        headers: { 'Content-Type': 'application/json; charset=utf-8' }
      }).then((result) => {
        return result.json()
        // return result.text()
      }).then(async (json) => {
        if (typeof json !== 'object') {
          json = JSON.parse(json)
        }
        this.response_text = {
          method: method,
          params: params,
          response: json
        }

        this.consoleLog(this.response_text)

        if (this.isResponseHasCaptcha(json)) { // capcha handling
          this.isCaptchaNecessary = true
          if (json.error && json.error.captcha_img) {
            this.captchaImg = json.error.captcha_img
          }
          if (json.error && json.error.captcha_sid) {
            params.captcha_sid = json.error.captcha_sid
          }

          while (this.isCaptchaNecessary) {
            this.consoleLog('ждем captcha')
            this.changeCaptchaTitle()
            await this.pause(1000)
          }
          params.captcha_key = this.captchaUserInputKey
          this.consoleLog('captcha got retry request with  this params')
          this.consoleLog(params)
          this.consoleLog('-------------------------------------------')
          json = this._onwApi_call(method, params)
        }
        return json
      })
        .catch(async (error) => {
          this.consoleLog('^^^^^^^^^^^^^^^')
          this.consoleLog(error)
          this.consoleLog('^^^^^^^метод^^^^^^')
          this.consoleLog(method)
          this.consoleLog('^^^^^параметры^^^^^^')
          this.consoleLog(params)
          this.consoleLog('ERROR API CALL: RETRY')
          if (method === 'upload_file') {
            if (!params.file_link) {
              console.error('BROKEN UPLOAD FILE; EXIT')
              return error
            }
          }
          this.logCurrentAction('Ошибка API запроса. Пробуем ещё раз.' + this.action_string_separator)
          await this.pause(1000)

          let response = await this._onwApi_call(method, params)
          return response
        })
    },

    logCurrentAction (actionString) {
      this.action_string += actionString
      if (arguments.length === 0) {
        this.action_string = ''
      }
    },

    pause (pausePeriod) {
      return new Promise(async (resolve) => {
        this.consoleLog('Pause ' + pausePeriod + ' ms began')
        setTimeout(() => resolve('Pause end'), pausePeriod)
      })
        .then(pauseEndMessage => {
          this.consoleLog(pauseEndMessage)
          return pauseEndMessage
        })
    },
    get_prog_bar_string (barString, perCent, bars, sep = ' ') {
      if (perCent > 100) {
        perCent = 100
      }
      let resultBarString = ''
      let barWeight = 100 / bars
      let fullBars = Math.floor(perCent / barWeight)
      let barPart = (perCent / barWeight) - Math.floor(perCent / barWeight)
      let partMax = Math.round(1 / ((1 / barWeight) - Math.floor(1 / barWeight)))

      for (let i = 1; i <= fullBars; i++) {
        resultBarString += barString[barString.length - 1] + sep
      }
      if (barPart !== 0) {
        let currentParts = barPart / (1 / partMax)
        let partCharIndex = Math.round(((barString.length - 1) / partMax * currentParts))
        resultBarString += barString[partCharIndex] + sep

        fullBars += 1
      }
      for (let i = fullBars; i < bars; i++) {
        resultBarString += barString[0] + sep
      }
      return resultBarString
    },
    change_title (perCent) {
      this.progress_bar_per_cent = perCent
      document.title = this.progress_bar + '( ' + this.current_product_number + '/' + this.$store.state.products.length + ' )'
    },
    saveLastExportedProductNumber (number) {
      window.localStorage.setItem('lastExportedProductNumber', number)
    },

    clearLastExportedProductNumber () {
      window.localStorage.removeItem('lastExportedProductNumber')
    }
  },
  mounted () {
    const lastNumber = window.localStorage.getItem('lastExportedProductNumber')
    if (lastNumber) {
      this.lastExportedProductNumber = lastNumber
    }
  }
}
</script>

<style scoped>
</style>
