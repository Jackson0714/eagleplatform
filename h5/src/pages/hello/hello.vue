<template>
  <div id="hello">
    {{echo}}
  </div>
</template>

<script>

export default {
  components: {

  },
  created () {
    window.document.title = 'hello'
  },
  data () {
    return {
      echo: 'hello h5'
    }
  },
  methods: {
  }
}
</script>

<i18n>
</i18n>

<style lang='less'>
  @import './hello.less';
</style>