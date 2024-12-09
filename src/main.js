import Vue from 'vue'
import ExportExcelApp from './ExportExcelApp.vue'
Vue.mixin({ methods: { t, n } })

const View = Vue.extend(ExportExcelApp)
new View().$mount('#accountexport')
