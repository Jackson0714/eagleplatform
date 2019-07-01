<template>
  <div class="user-avator-dropdown">
    <Dropdown @on-click="handleClick">
      <Avatar :src="userAvator" />
      <Icon :size="18" type="md-arrow-dropdown"></Icon>
      <DropdownMenu slot="list">
        <DropdownItem name="password">修改密码</DropdownItem>
        <DropdownItem name="logout">退出登录</DropdownItem>
      </DropdownMenu>
    </Dropdown>
    <Modal v-model="modelView" title="修改密码" :footer-hide='true' @on-cancel="handleCancel">
      <Form :label-width="80">
        <FormItem label="原密码">
          <Input type="password" style="width: 350px" :maxlength="30" v-model="origin"></Input>
        </FormItem>
        <FormItem label="新密码">
          <Input type="password" style="width: 350px" :maxlength="30" v-model="password"></Input>
        </FormItem>
        <FormItem label="确认密码">
          <Input type="password" style="width: 350px" :maxlength="30" v-model="reInput"></Input>
          <div class="upload-picture-tip">密码只能由字母、数字、_和.组成</div>
        </FormItem>
      </Form>
      <Button size="large" style="margin-left: 330px" @click="handleCancel()">取消</Button>
      <Button type="primary" size="large" style="margin-left: 20px" @click="handleChangePassword()">保存</Button>
    </Modal>
  </div>
</template>

<script>
import './user.less'
import { mapActions } from 'vuex'
import { changePassword } from '@/api/user'
export default {
  name: 'User',
  props: {
    userAvator: {
      type: String,
      default: ''
    },
    userName: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      modelView: false,
      password: '',
      origin: '',
      reInput: ''
    }
  },
  methods: {
    ...mapActions([
      'handleLogOut'
    ]),
    handleClick (name) {
      switch (name) {
        case 'logout':
          this.handleLogOut().then(() => {
            this.$router.push({
              name: 'login'
            })
          })
          break
        case 'password':
          this.modelView = true
          break
      }
    },
    handleChangePassword () {
      var tip = ''
      if (this.origin === '' || this.password === '' || this.reInput === '') {
        tip = '修改失败，密码不能为空'
      } else if (this.origin === this.password) {
        tip = '修改失败，新密码不能和原密码一样'
      } else if (this.password !== this.reInput) {
        tip = '修改失败，新密码两次输入不一致'
      } else if (!(/^[a-zA-Z0-9._]+$/.test(this.password)) || this.password.length < 6) {
        tip = '修改失败，新密码格式有误'
      }
      if (tip !== '') {
        this.$Message.error(tip)
      } else {
        const data = {
          userName: this.userName,
          origin: this.origin,
          password: this.password,
          reInput: this.reInput
        }
        changePassword({ data }).then(res => {
          if (res.msg === 'ok') {
            this.$Message.success('修改成功')
            this.modelView = false
            this.handleCancel()
          } else if (res.msg === 'wrongPassword') {
            this.$Message.error('原密码错误，修改失败')
          } else {
            this.$Message.error('修改失败，请稍后再试')
          }
        })
      }
    },
    handleCancel () {
      this.origin = ''
      this.password = ''
      this.reInput = ''
      this.modelView = false
    }
  }
}
</script>
