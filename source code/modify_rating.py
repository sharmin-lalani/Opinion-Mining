import MySQLdb

import math
import string

#initilize dabase connection variables
host="127.0.0.1" # Host name 
username="root" # Mysql username 
password="" # Mysql password 
db_name="market_analysis" # Database name 



# Open database connection
db = MySQLdb.connect(host, username, password, db_name)

# prepare a cursor object using cursor() method
cursor = db.cursor()
cursor4 = db.cursor()

# Get count of buy and sell sentiments
sql = "SELECT tag_id,count(sentiment)as c2,valid_till  FROM `tweet_tags`, tweets where sentiment='b' and tweets.tweet_id=tweet_tags.tweet_id and valid_till='2014-03-09' group by tag_id  "

sql4 = "SELECT tag_id,count(sentiment)as c2, valid_till  FROM `tweet_tags`, tweets where sentiment='s' and tweets.tweet_id=tweet_tags.tweet_id and valid_till='2014-03-09' group by tag_id "

res = {}
	
try:
  # Execute buy
	cursor.execute(sql)
	results = cursor.fetchall()
	for row in results: 
		res[row[0]] = [row[1],'b']
		
	# Execute sell
		
	cursor4.execute(sql4)
	results4 = cursor4.fetchall()
	for row in results4: 
		if row[0] in res.keys():
				sellc = row[1]
				buyc = res[row[0]][0]
				if sellc > buyc :
					print 's>b'
					res[row[0]][1] = 's'
				res[row[0]][0] = buyc + sellc
		else:
			res[row[0]] = [row[1],'s']
		
	
	
except:
	print "Error: unable to fetch data"

finally:
	cursor.close() #close anyway
	cursor4.close()

cursor1 = db.cursor()
cursor2 = db.cursor()
cursor3 = db.cursor()
sql1 = "select user_id from followers"


try:
	
	cursor1.execute(sql1)
	result1 = cursor1.fetchall()
	cursor1.close()
	for row in result1:
		user = row[0]
		res1 = {}
		print user
		# query for buy
		sql2 = "SELECT tag_id,count(sentiment)as c2, valid_till, user_id  FROM `tweet_tags`, tweets where sentiment='b'  and  tweets.tweet_id=tweet_tags.tweet_id and valid_till='2014-03-09' and user_id = '%d' group by tag_id" %(user)
		cursor2.execute(sql2)
		result2 = cursor2.fetchall()
		for r in result2: 
			res1[r[0]] = [r[1], 0, 'b']
		# query for sell
		
		sql3 = "SELECT tag_id,count(sentiment)as c2, valid_till, user_id  FROM `tweet_tags`, tweets where sentiment='s'  and  tweets.tweet_id=tweet_tags.tweet_id and valid_till='2014-03-09' and user_id = '%d' group by tag_id" %(user)
		cursor3.execute(sql3)
		result3 = cursor3.fetchall()
		
		for r in result3:
			if r[0] in res1.keys():
				sellc = r[1]
				buyc = res1[r[0]][0]
				if sellc > buyc :
					res1[r[0]][2] = 's'
				res1[r[0]][1] = sellc
			else:
				res1[r[0]] = [0, r[1],'s']
		#print res1
		
		no_of_comp = 0
		delta = 0.0
		
		for e in res1.keys():
			analyst_buy_count = res1[e][0]
			analyst_sell_count = res1[e][1]
			analyst_count = analyst_buy_count + analyst_sell_count
			#analyst_sen = res1[e][2]
			overall_count = res[e][0]
			overall_sen = res[e][1]
			no_of_comp = no_of_comp + 1
			
			change = 0.0
			if overall_sen == 'b':
				change = analyst_buy_count - analyst_sell_count
			else:
				change = analyst_sell_count - analyst_buy_count
				
			change = change/analyst_count
			contri = float(analyst_count)/overall_count
			delta = delta + change - contri
			#print "hi", change, contri, delta
		if no_of_comp != 0:
			#print delta, no_of_comp
			delta = delta/no_of_comp
		print delta
		cursor5 = db.cursor()
		cursor6 = db.cursor()
		
		
		if no_of_comp != 0 and delta != 0 :
			sql5 = "select rating from followers where user_id = '%d'" %( user)
			
			cursor5.execute(sql5)
			result5 = cursor5.fetchall()
			for r in result5:
				rating = r[0] + delta
				if rating < 0 :
					rating = 0
				if rating > 5 :
					rating = 5
				sql6 = "update followers set rating = '%f' where user_id = '%d'" %(rating, user)
				
				cursor6.execute(sql6)
				db.commit()
	
except:
	print "Error: unable to fetch data"

finally:
	 #close anyway
	cursor2.close()	
	cursor3.close()
	cursor5.close()
	cursor6.close()
# disconnect from server
db.close()	
#print res



			



