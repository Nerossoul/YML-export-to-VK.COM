'use strict'
const merge = require('webpack-merge')
const prodEnv = require('./prod.env')

module.exports = merge(prodEnv, {
  NODE_ENV: '"development"',
  REDIRECT_URL: '"https://playavto.ru/vk_export/static/api/vk_auth.php?env=dev"',
  OWN_API_SERVER: '"https://playavto.ru/vk_export/static/api/"'
})
