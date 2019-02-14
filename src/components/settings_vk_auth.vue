<template>
  <div>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Авторизация в контакте:</h5>
      <div v-if="this.$store.state.access_token == 'no_token'">
        Вы не авторизованы!!!<br>
        <a type="button" :href="user_auth_link_to_get_code" name="button" class="btn btn-dark">Авторизоваться</a><br>
      </div>
      <div v-else>
        ВЫ успешно авторизованы<br>
        Ваш токен: {{ this.$store.state.access_token }}
      </div>
    </div>
    <div class="card-body">
      <h5 class="card-title">Укажите группу:</h5>
      Group ID. <br>Укажите 1 раз убдет сохранена в вашем браузере. но пока сохранение не рабтает <br>
      <input v-model="localGroupId" type="text">{{ this.$store.state.group_id }}<br>
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
      redirectUrl: 'https://playavto.ru/vk_export/server/vk_auth.php',
      user_auth_link_to_get_code: 'https://oauth.vk.com/authorize?client_id=6860578&display=popup&redirect_uri=https://playavto.ru/vk_export/server/vk_auth.php&response_type=code&scope=134479876&v=5.92'
    }
  },
  watch: {
    localGroupId () {
      this.$store.state.group_id = this.localGroupId
    }
  },
  mounted: function () {
    if( typeof this.$route.query.access_token != 'undefined' ) {
      this.$store.state.access_token = this.$route.query.access_token
      this.$router.push('/settings')
    }
  }
}
</script>

<style scoped>

</style>
