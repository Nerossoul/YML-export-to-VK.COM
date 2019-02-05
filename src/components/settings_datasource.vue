<template>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Текущий источник данных</h5>
      <p class="card-text">{{ymlSourceLink}}</p>
      <div v-if="isGettingDataNow">{{gettingDataText}}<br>
        <div class="spinner-border" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div v-else >
        <button type="button" name="button" class="btn btn-dark" @click="getData">Получить данные</button>
        <button type="button" name="button" class="btn btn-dark" @click="xmlToJson2">РАЗОБРАТЬ ПОЛУЧЕНЫЙ ДОКУМЕНТ</button>
      </div>
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import xml2json from '@/assets/xml2json'


export default {
  name: 'settings_datasource',
  data () {
    return {
      // TODO: ymlSourceLink must be user setable and saved to localstorege
      ymlSourceLink: 'https://playavto.ru/export/yandex_yml.xml',
      xmlText: '!!! ',
      xmlDocument: {},
      xmlObj: {},
      isGettingDataNow: false,
      gettingDataText: 'Получаем данные...'
      xml2json: xml2json
    }
  },
  watch: {
    xmlText()  {
      this.xmlDocument = this.StringToXML()
    }
  },
  methods: {
    getData () {
      this.isGettingDataNow = true
      this.file_get_contents()
      // https://lage.us/Javascript-File-Get-Contents.html
    },
    file_get_contents () {
      var context = this
      fetch(this.ymlSourceLink)
      .then((resp) => {
          return resp.text()
        })
      .then(function (data) {
        context.xmlText = data
        context.isGettingDataNow = false
      });
      },
      xmlToJson2 (i) {
        i++;
        console.log(i + 'lunched')
        setTimeout(()=>{
          this.xmlToJson2(i);
        }, 2000)
        console.log(i +'fineshed')
        //http://jsfiddle.net/abdmob/gkxucxrj/1/
      },
     xmlToJson () {
        console.log('Запустили парсинг')
        var obj = {};
        var xml = this.xmlDocument;
        if (xml.nodeType == 1) { // element
          // do attributes
          if (xml.attributes.length > 0) {
          obj["@attributes"] = {};
            for (var j = 0; j < xml.attributes.length; j++) {
              var attribute = xml.attributes.item(j);
              obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
            }
          }
        } else if (xml.nodeType == 3) { // text
          obj = xml.nodeValue;
        }
        // do children
        if (xml.hasChildNodes()) {
          for(var i = 0; i < xml.childNodes.length; i++) {
            var item = xml.childNodes.item(i);
            var nodeName = item.nodeName;
            if (typeof(obj[nodeName]) == "undefined") {
              console.log('first if')
              obj[nodeName] = this.xmlToJson(item);
            } else {
              if (typeof(obj[nodeName].push) == "undefined") {
                var old = obj[nodeName];
                obj[nodeName] = [];
                obj[nodeName].push(old);
              }
              console.log('seconf if')
              obj[nodeName].push(this.xmlToJson(item));
            }
          }
        }
        this.xmlObj = obj;
      },
      StringToXML() {
         let oString = this.xmlText;
         if (window.ActiveXObject) {
         var oXML = new ActiveXObject("Microsoft.XMLDOM"); oXML.loadXML(oString);
         return oXML;
         }
         // code for Chrome, Safari, Firefox, Opera, etc.
         else {
         return (new DOMParser()).parseFromString(oString, "text/xml");
         }
       }
  }
}
</script>

<style scoped>

</style>
