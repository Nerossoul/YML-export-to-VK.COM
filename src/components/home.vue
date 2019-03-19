<template>
  <div class="mt-3">
    <h2>{{ msg }}</h2>
    Good instruction will appear here some day. But now just click the button below <br>
    <router-link tag="button" type="button" class="btn btn-dark" to="/settings" >
        Go to Settings
      </router-link>
  </div>
</template>

<script>
export default {
  name: 'Home',
  data () {
    return {
      msg: 'Home',
    }
  }
}
</script>

<style scoped>

</style>
