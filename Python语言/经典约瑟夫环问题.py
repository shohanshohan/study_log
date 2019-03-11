#!/usr/bin/python3

# ===================================================================
# 约瑟夫环（约瑟夫问题）是一个数学的应用问题：
# 已知n个人（以编号1，2，3...n分别表示）围坐在一张圆桌周围。
# 从编号为k的人开始报数，数到m的那个人出列；他的下一个人又从1开始报数，数到m的那个人又出列；
# 依此规律重复下去，直到圆桌周围的人全部出列或规定留下的人数
# ====================================================================

def yuesefu(nums, step, stay):
    #参数 nums：人数，step: 数到几的步数，stay: 最后留下多少人
    lists = list(range(1, nums+1))
    check = 0
    while len(lists) > stay:
        for i in lists[:]:
            check += 1
            if check == step:
                check = 0
                lists.remove(i)
                print("{}号下船了".format(i))
    return lists



stays = yuesefu(30, 5, 1)
print("最后留下的人：", stays)
