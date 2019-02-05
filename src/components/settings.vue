<template>
  <div>
    <h2>{{ msg }}</h2>
    <div class="row">
       <div class="col-sm">
         <data-source></data-source>
       </div>
    </div>
  </div>
</template>

<script>
import DataSource from '@/components/settings_datasource'

export default {
  name: 'Settings',
  data () {
    return {
      msg: 'Settings'
    }
  },
  components: {
    DataSource
  }
}
</script>

<style scoped>

</style>
