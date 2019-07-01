<template>
  <div>
    <Row :gutter="20">
      停车支付,hello
      <DatePicker type="date" multiple placeholder="Select date" style="width: 300px"></DatePicker>
    </Row>
  </div>
</template>

<script>
export default {
  name: 'stop_pay',
  data () {
    return {
      value1: 0
    }
  }
}
</script>

<style>

</style>
