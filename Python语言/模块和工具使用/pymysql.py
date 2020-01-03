#！/usr/bin/python3  
#encoding=utf-8  
import pymysql

class DataBaseHandle(object):
    ''' 定义一个 MySQL 操作类'''
    def __init__(self,host,username,password,database,port):
        '''初始化数据库信息并创建数据库连接'''
        self.db = pymysql.connect(host,username,password,database,port,charset='utf8')

    def insertDB(self,sql):
        ''' 插入数据库操作 '''
        self.cursor = self.db.cursor()
        try:
            # 执行sql
            res = self.cursor.execute(sql)  
            self.db.commit()
            return res # 返回 插入数据 条数 可以根据 返回值 判定处理结果
        except:
            # 发生错误时回滚
            self.db.rollback()
            return False
        finally:
            self.cursor.close()

    def updateDb(self,sql):
        ''' 更新数据库操作 '''
        self.cursor = self.db.cursor()
        try:
            # 执行sql
            res = self.cursor.execute(sql) 
            self.db.commit()
            return res # 返回 更新数据 条数 可以根据 返回值 判定处理结果
        except:
            # 发生错误时回滚
            self.db.rollback()
            return False
        finally:
            self.cursor.close()

    def selectDb(self,sql):
        ''' 数据库查询 '''
        self.cursor = self.db.cursor(pymysql.cursors.DictCursor) #返回字典式数据
        try:
            self.cursor.execute(sql) #执行sql
            return self.cursor.fetchall() # 返回所有记录列表
        except:
            return False
        finally:
            self.cursor.close()

    def findDb(self,sql):
        ''' 数据库查询,一条数据 '''
        self.cursor = self.db.cursor(pymysql.cursors.DictCursor) #返回字典式数据
        try:
            self.cursor.execute(sql) #执行sql
            return self.cursor.fetchone() # 返回一条记录
        except:
            return False
        finally:
            self.cursor.close()

    def closeDb(self):
        ''' 数据库连接关闭 '''
        self.db.close()
		
