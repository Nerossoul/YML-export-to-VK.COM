<template>
  <div>
    <h2>{{ msg }}</h2>
    <div class="row">
       <div class="col-sm">
         <data-source></data-source>
         <vk-auth></vk-auth>
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
