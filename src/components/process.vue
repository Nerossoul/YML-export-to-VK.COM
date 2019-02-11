<template>
<div>
    <h2>{{ msg }}</h2>
    {{ store_test }}
</div>
</template>

<script>
export default {
  name: 'Process',
  data () {
    return {
      msg: 'Process room'
    }
  },
  computed: {
    store_test () {
      return this.$store.state.access_token
    }
  }
}
</script>

<style scoped>

</style>
