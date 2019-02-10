<template>
  <div>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Авторизация в контакте:</h5>
      <p class="card-text">{{user_auth_link_to_get_code}}</p>
      <a type="button" name="button" class="btn btn-dark">Авторизоваться</a>
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
      user_auth_link_to_get_code: 'https://oauth.vk.com/authorize?client_id=6848088&display=page&response_type=code&scope=134479876&v=5.92',
      group_auth_link_to_get_access_token: 'https://oauth.vk.com/access_token?',
      access_token: '0c42faa89e4ecf76ec401832257954c0622be8c0d4522cbfd56d829dde400bda489fc206768e5f3019a39'
    }
  }
}
</script>

<style scoped>

</style>
