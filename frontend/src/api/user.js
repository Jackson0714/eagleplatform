import axios from '@/libs/api.request'

export const login = ({ userName, password }) => {
  const data = {
    userName,
    password
  }
  return axios.request({
    url: 'backend-user/backend-login',
    data,
    method: 'post'
  })
}

export const getUserInfo = (token) => {
  return axios.request({
    url: 'backend-user/user-info',
    params: {
      token
    },
    method: 'get'
  })
}

export const logout = (token) => {
  return axios.request({
    url: 'backend-user/logout',
    method: 'post'
  })
}

export const createUser = ({ data }) => {
  return axios.request({
    url: 'backend-user/create-user',
    data,
    method: 'post'
  })
}

export const changePassword = ({ data }) => {
  return axios.request({
    url: 'backend-user/change-password',
    data,
    method: 'post'
  })
}

export const getUserList = ({page}) => {
  return axios.request({
    url: 'backend-user/get-user-list?page=' + page + '&per-page=10',
    method: 'get'
  })
}

export const changeRouter = ({ data }) => {
  return axios.request({
    url: 'backend-user/change-router',
    data,
    method: 'post'
  })
}

export const resetUser = ({ data }) => {
  return axios.request({
    url: 'backend-user/reset-user',
    data,
    method: 'post'
  })
}

export const deleteUser = ({ data }) => {
  return axios.request({
    url: 'backend-user/delete-user',
    data,
    method: 'post'
  })
}
