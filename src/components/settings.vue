<template>
  <div class="mt-3">
    <h2>{{ msg }}</h2>
    <div class="row">
       <div class="col-sm">
         <vk-auth></vk-auth>
       </div>
    </div>
    <div class="row">
       <div class="col-sm" v-if="this.$store.state.access_token != 'no_token'">
         <data-source></data-source>
       </div>
    </div>
  </div>
</template>

<script>
import DataSource from '@/components/settings_datasource'
import VkAuth from '@/components/settings_vk_auth'

export default {
  name: 'Settings',
  data () {
    return {
      msg: 'Settings'
    }
  },
  components: {
    DataSource,
    VkAuth
  }
}
</script>

<style scoped>

</style>
