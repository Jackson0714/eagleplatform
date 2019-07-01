<template>
  <Layout style="height: 100%" class="main">
    <Sider hide-trigger collapsible :width="256" :collapsed-width="64" v-model="collapsed" class="left-sider" :style="{overflow: 'hidden'}">
      <side-menu accordion ref="sideMenu" :active-name="$route.name" :collapsed="collapsed" @on-select="turnToPage" :menu-list="menuList">
        <div class="logo-con">
          <img v-show="!collapsed" :src="maxLogo" key="max-logo" />
          <img v-show="collapsed" :src="maxLogo" key="min-logo" />
        </div>
      </side-menu>
    </Sider>
    <Layout>
      <Header class="header-con">
        <header-bar :collapsed="collapsed" @on-coll-change="handleCollapsedChange">
          <user :user-avator="userAvator" :user-name="usersName" />
          <Tooltip content="账户管理" v-show="name == 'admin'">
            <Icon @click="handleGetAccount(page)" style=" margin-right:15px;font-size:25px" type="md-person" />
          </Tooltip>
          <fullscreen v-model="isFullscreen" style="margin-right: 15px;" />
        </header-bar>
      </Header>
      <Content class="main-content-con">
        <Layout class="main-layout-con">
          <!-- <div class="tag-nav-wrapper">
            <tags-nav :value="$route" @input="handleClick" :list="tagNavList" @on-close="handleCloseTag"/>
          </div> -->
          <Content class="content-wrapper">
            <keep-alive :include="cacheList">
              <router-view />
            </keep-alive>
          </Content>
        </Layout>
      </Content>
    </Layout>
    <Modal v-model="ModelView" title="新建账户" :footer-hide='true' @on-cancel="handleCancel">
      <Form :label-width="80">
        <FormItem label="账户">
          <Input style="width: 350px" :maxlength="30" v-model="userName"></Input>
          <div class="upload-picture-tip">用户名只能由字母、数字、_和.组成</div>
          <div class="upload-picture-tip">初始密码为：abc123_</div>
        </FormItem>
      </Form>
      <Button size="large" style="margin-left: 330px" @click="handleCancel()">取消</Button>
      <Button type="primary" size="large" style="margin-left: 20px" @click="handleCreateUser()">保存</Button>
    </Modal>
    <Drawer title="Basic Drawer" width="720" :styles="styles" :closable="false" v-model="AccountView">
      <p slot="header">
        <span>账户管理</span>
        <Tooltip content="新建账户" v-show="name == 'admin'" style="margin-left:580px;">
          <Icon @click=" ModelView = true " style="font-size:25px" type="md-add" />
        </Tooltip>
      </p>
      <tables ref="tables" editable searchable search-place="top" v-model="tableData" :columns="columns" />
      <Page :total="userCount" :current="page" style="margin-top:20px ;" :page-size="10" @on-change="handleGetAccount" />
    </Drawer>
  </Layout>
</template>
<script>
import SideMenu from './components/side-menu'
import HeaderBar from './components/header-bar'
import TagsNav from './components/tags-nav'
import User from './components/user'
import Fullscreen from './components/fullscreen'
import Language from './components/language'
import { mapMutations, mapActions } from 'vuex'
import { createUser, getUserList, changeRouter, resetUser, deleteUser } from '@/api/user'
import { getNewTagList, getNextRoute, routeEqual } from '@/libs/util'
import minLogo from '@/assets/images/logo-min.jpg'
import maxLogo from '@/assets/images/logo-min.jpg'
import Tables from '_c/tables'
import './main.less'
export default {
  name: 'Main',
  components: {
    SideMenu,
    HeaderBar,
    Language,
    TagsNav,
    Fullscreen,
    User,
    Tables
  },
  data () {
    return {
      styles: {
        height: 'calc(100% - 55px)',
        overflow: 'auto',
        paddingBottom: '53px',
        position: 'static'
      },
      columns: [
        { title: '用户名', key: 'name', width: 120 },
        { title: '创建时间', key: 'createdAt' },
        {
          title: '权限',
          key: 'action',
          width: 280,
          render: (h, params) => {
            return h('Select', {
              props: {
                size: 'small',
                multiple: true,
                value: this.tableData[params.index].accountRouter,
                placeholder: '设置权限'
              },
              on: {
                'on-change': (event) => {
                  const data = {
                    'id': this.tableData[params.index]._id,
                    'router': event
                  }
                  changeRouter({ data }).then(res => {
                    if (res.msg !== 'ok') {
                      this.$Message.error('修改失败，请稍后再试')
                    }
                  })
                }
              }
            }, this.routerList.map((item) => {
              return h('Option', {
                props: {
                  value: item.value,
                  label: item.label
                }
              })
            }))
          }
        },
        {
          title: '操作',
          key: 'action',
          width: 125,
          render: (h, params) => {
            return h('div', [
              h('Poptip', {
                props: {
                  confirm: true,
                  title: '确定要重置账户吗?'
                },
                style: {
                  marginRight: '5px',
                  zIndex: 9999
                },
                on: {
                  'on-ok': () => {
                    const data = {
                      'id': this.tableData[params.index]._id
                    }
                    resetUser({ data }).then(res => {
                      if (res.msg === 'ok') {
                        this.$Message.success('重置成功')
                        this.handleGetAccount(this.page)
                      } else {
                        this.$Message.error('重置失败，请稍后再试')
                      }
                    })
                  }
                }
              },
              [
                h('i-button', {
                  props: {
                    type: 'primary',
                    size: 'small'
                  }
                }, '重置')
              ]),
              h('Poptip', {
                props: {
                  confirm: true,
                  title: '你确定要删除吗?',
                  'z-index': 9999
                },
                on: {
                  'on-ok': () => {
                    const data = {
                      'id': this.tableData[params.index]._id
                    }
                    deleteUser({ data }).then(res => {
                      if (res.code === 200) {
                        this.$Message.success('删除成功')
                        this.handleGetAccount(this.page)
                      } else {
                        this.$Message.error('删除失败，请稍后再试')
                      }
                    })
                  }
                }
              },
              [
                h('i-button', {
                  props: {
                    type: 'error',
                    size: 'small'
                  }
                }, '删除')
              ])
            ])
          }
        }
      ],
      routerList: [],
      tableData: [],
      accountRouter: [],
      collapsed: false,
      minLogo,
      maxLogo,
      userName: '',
      name,
      isFullscreen: false,
      ModelView: false,
      AccountView: false,
      userCount: 0,
      page: 1
    }
  },
  computed: {
    tagNavList () {
      return this.$store.state.app.tagNavList
    },
    tagRouter () {
      return this.$store.state.app.tagRouter
    },
    userAvator () {
      return this.$store.state.user.avatorImgPath
    },
    usersName () {
      return this.$store.state.user.userName
    },
    cacheList () {
      return this.tagNavList.length ? this.tagNavList.filter(item => !(item.meta && item.meta.notCache)).map(item => item.name) : []
    },
    menuList () {
      if (this.$store.state.user.userName === 'admin') {
        return this.$store.getters.menuList
      }
      var res = []
      for (let item of this.$store.getters.menuList) {
        if (this.$store.state.user.accountRouter.indexOf(item.children[0].meta.title) !== -1) {
          res.push(item)
        }
      }
      return res
    },
    local () {
      return this.$store.state.app.local
    }
  },
  methods: {
    ...mapMutations([
      'setBreadCrumb',
      'setTagNavList',
      'addTag',
      'setLocal'
    ]),
    ...mapActions([
      'handleLogin'
    ]),
    turnToPage (route) {
      let { name, params, query } = {}
      if (typeof route === 'string') name = route
      else {
        name = route.name
        params = route.params
        query = route.query
      }
      if (name.indexOf('isTurnByHref_') > -1) {
        window.open(name.split('_')[1])
        return
      }
      this.$router.push({
        name,
        params,
        query
      })
    },
    handleCollapsedChange (state) {
      this.collapsed = state
    },
    handleCreateUser () {
      if (this.userName === '' || !(/^[a-zA-Z0-9._]+$/.test(this.userName)) || this.userName.length < 6) {
        this.$Message.error('用户名输入有误，请验证后重试')
      } else {
        const data = {
          userName: this.userName
        }
        createUser({ data }).then(res => {
          if (res.msg === 'ok') {
            this.$Message.success('创建成功')
            this.ModelView = false
            this.handleCancel()
          } else if (res.msg === 'hasUserName') {
            this.$Message.error('用户名重复，创建失败')
          } else {
            this.$Message.error('创建失败，请稍后再试')
          }
        })
      }
      this.handleGetAccount(this.page)
    },
    handleCancel () {
      this.userName = ''
      this.ModelView = false
    },
    handleCloseTag (res, type, route) {
      let openName = ''
      if (type === 'all') {
        this.turnToPage('home')
        openName = 'home'
      } else if (routeEqual(this.$route, route)) {
        if (type === 'others') {
          openName = route.name
        } else {
          const nextRoute = getNextRoute(this.tagNavList, route)
          this.$router.push(nextRoute)
          openName = nextRoute.name
        }
      }
      this.setTagNavList(res)
      this.$refs.sideMenu.updateOpenName(openName)
    },
    handleClick (item) {
      this.turnToPage(item)
    },
    handleGetAccount (value) {
      this.AccountView = true
      this.page = value
      const page = value
      getUserList({ page }).then(res => {
        if (res.msg === 'ok') {
          this.tableData = res.data.items
          this.userCount = res.data.totalCount
        } else {
          this.$Message.error('获取失败，请稍后再试')
        }
      })
      this.routerList = []
      for (let item of this.$store.getters.menuList) {
        this.routerList.push({ value: item.children[0].meta.title, label: item.children[0].meta.title })
      }
    }
  },
  watch: {
    '$route' (newRoute) {
      this.setBreadCrumb(newRoute.matched)
      this.setTagNavList(getNewTagList(this.tagNavList, newRoute))
    }
  },
  mounted () {
    /**
     * @description 初始化设置面包屑导航和标签导航
     */
    this.setTagNavList()
    this.addTag({
      route: this.$store.state.app.homeRoute
    })
    this.setBreadCrumb(this.$route.matched)
    // 设置初始语言
    this.setLocal(this.$i18n.locale)
    this.name = this.$store.state.user.userName
    // // 文档提示
    // this.$Notice.info({
    //   title: '想快速上手，去看文档吧',
    //   duration: 0,
    //   render: (h) => {
    //     return h('p', {
    //       style: {
    //         fontSize: '13px'
    //       }
    //     }, [
    //       '点击',
    //       h('a', {
    //         attrs: {
    //           href: 'https://lison16.github.io/iview-admin-doc/#/',
    //           target: '_blank'
    //         }
    //       }, 'iview-admin2.0文档'),
    //       '快速查看'
    //     ])
    //   }
    // })
  }
}
</script>
