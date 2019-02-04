var clock = new Vue({
    el: '#main',
    data: {
      main: "new"
    },
    computed: {
      logined() {
        return true;
      }
    },
    components: {
      'site-login': siteLogin,
      'dash-board' : dashBoard

    }
});
