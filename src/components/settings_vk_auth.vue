<template>
  <div>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Авторизация в контакте:</h5>
      <!-- a type="button" :href="user_auth_link_to_get_code" name="button" target="_blank" class="btn btn-dark">Get code</a><br -->
      Group ID
      <input v-model="localGroupId" type="text">{{ this.$store.state.group_id }}<br>
      <!-- a type="button" :href="group_auth_link_to_get_access_token" name="button" target="_blank" class="btn btn-dark">Get access token</a><br -->
      Вставьте ваш токен сюда.
      <input v-model="localAccessToken" type="password">
      {{ this.$store.state.access_token }}
    </div>
  </div>
</div>
</template>

<script>
/* eslint-disable */
export default {
  name: 'settings_vk_auth',
  data () {
    return {
      localGroupId: '',
      user_auth_link_to_get_code: 'https://oauth.vk.com/authorize?client_id=6848088&display=page&response_type=code&scope=134479876&v=5.92',
      group_auth_link_to_get_access_token: 'https://oauth.vk.com/access_token?',
      localAccessToken: ''
    }
  },
  watch: {
    localAccessToken () {      
      this.$store.state.access_token = this.localAccessToken
    },
    localGroupId () {
      this.$store.state.group_id = this.localGroupId
    }
  }
}
</script>

<style scoped>

</style>
