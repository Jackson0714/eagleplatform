import Mock from 'mockjs'
import { doCustomTimes } from '@/libs/util'
const Random = Mock.Random

export const getTableData = req => {
  let tableData = []
  doCustomTimes(10, () => {
    tableData.push(Mock.mock({
      plateNumber: '鄂A'+Random.guid(),
      name: '@cname',
      phone: /^1[385][0-9]\d{8}/,
      local: Random.pick(['武汉光谷新世界','武汉新世界中心','武汉新世界国贸大厦','武汉时代·新世界']),
      parkingTime: '@date(yyyy-MM-dd HH:mm)',
      payTime: '@date(yyyy-MM-dd HH:mm)',
      pay: Random.pick([20,40,50,60,80,100])
    }))
  })
  return {
    code: 200,
    data: tableData,
    msg: ''
  }
}

export const getDragList = req => {
  let dragList = []
  doCustomTimes(5, () => {
    dragList.push(Mock.mock({
      name: Random.csentence(10, 13),
      id: Random.increment(10)
    }))
  })
  return {
    code: 200,
    data: dragList,
    msg: ''
  }
}
