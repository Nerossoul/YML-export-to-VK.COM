import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    products: [],
    categories: [],
    photoBase: {},
    badPhotoIds: [],
    access_token: 'no_token',
    group_id: '81204119',
    sourceDate: '',
    CategoryToAlbumId: {
      '1': 1, // ok
      '2': 13,
      '3': 2,
      '4': 14,
      '5': 3,
      '32': 4,
      '33': 5,
      '64': 6,
      '66': 7,
      '67': 8,
      '68': 9,
      '70': 10,
      '72': 15,
      '73': 11,
      '90': 0,
      '156': 17 // ok
    }
  }
})
