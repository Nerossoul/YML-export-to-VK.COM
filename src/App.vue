<template>
  <div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">YML EXPORT</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <router-link tag="li" class="nav-item" exact to="/" active-class="active">
        <a class="nav-link" >Home </a>
      </router-link>
      <router-link tag="li" class="nav-item" to="/settings" active-class="active">
        <a class="nav-link">Settings</a>
      </router-link>
      <li class="nav-item">
        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Process</a>
      </li>
    </ul>
  </div>
</nav>
    <div class="container-fluid">
      <router-view/>
    </div>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
#app {}
</style>
