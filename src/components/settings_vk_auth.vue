<template>
  <div>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Авторизация в контакте:</h5>
      <p class="card-text">{{user_auth_link_to_get_code}}</p>
      <a type="button" :href="user_auth_link_to_get_code" name="button" target="_blank" class="btn btn-dark">Get code</a><br>
      ваш код
      <input v-model="user_code" type="password"><br>
      id приложения
      <input v-model="client_id" type="password"><br>
      секретный ключ приложения
      <input v-model="client_secret" type="password"><br>
      <a type="button" :href="group_auth_link_to_get_access_token" name="button" target="_blank" class="btn btn-dark">Get access token</a><br>
      Вставьте ваш токен сюда.
      <input v-model="access_token" type="password"><br>
      <a type="button" @click="testapi" name="button" target="_blank" class="btn btn-dark">Get access token</a><br>
    </div>
  </div>
</div>
</template>

<script>

export default {
  name: 'settings_vk_auth',
  data () {
    return {
      group_id: '',
      user_auth_link_to_get_code: 'https://oauth.vk.com/authorize?client_id=6848088&display=page&response_type=code&scope=134479876&v=5.92',
      group_auth_link_to_get_access_token: 'https://oauth.vk.com/access_token?',
      user_code: '',
      access_token: '',
      client_id: '',
      client_secret: ''
    }
  },
  watch: {
    access_token () {
      this.$store.state.access_token = this.access_token
    }
  },
  methods: {
    testapi () {

    }
  }
}
</script>

<style scoped>

</style>
