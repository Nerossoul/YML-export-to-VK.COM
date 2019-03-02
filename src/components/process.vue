<template>
<div class="mt-3">
    <h2>{{ msg }}</h2>
    <div class="alert alert-primary" role="alert" v-if="!active_link.activeLink">
      {{ active_link.message }} <router-link to='/settings'>НАСТРОИТЬ </router-link>
    </div>
    <div v-else>
      <vk-api></vk-api>
    </div>
</div>
</template>

<script>
/* eslint-disable */
import vkApi from '@/components/process_vk_api'

export default {
  name: 'Process',
  data () {
    return {
      msg: 'Process room'
    }
  },
  computed: {
    active_link () {
      let result = {
        message: '',
        activeLink: true
      }
      if (this.$store.state.products.length < 1) {
        result.message += 'Не получены товары. '
        result.activeLink = false
      }
      if (this.$store.state.categories.length < 1) {
        result.message += 'Не получены категории. '
        result.activeLink = false
      }
      if (this.$store.state.access_token === 'no_token') {
        result.message += 'Нет токена авторизации. '
        result.activeLink = false
      }
      if (this.$store.state.group_id === '') {
        result.message += 'Group Id expected. '
        result.activeLink = false
      }
      if (result.message === '') {
        result.activeLink = true
      }
      return result
    }
  },
  components: {
    vkApi
  }
}
</script>

<style scoped>

</style>
