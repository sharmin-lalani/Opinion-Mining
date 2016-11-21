import MySQLdb
import re
import math
import string
from nltk.corpus import wordnet as wn
from nltk.stem.wordnet import WordNetLemmatizer

#initilize dabase connection variables
host="127.0.0.1" # Host name 
username="root" # Mysql username 
password="" # Mysql password 
db_name="market_analysis" # Database name 



# Open database connection
db = MySQLdb.connect(host, username, password, db_name)

#start process_tweet
def processTweet(tweet):
    #Convert to lower case
    tweet = tweet.lower()

    #Convert www.* or https?://* to URL
    tweet = re.sub('((www\.[\s]+)|(https?://[^\s]+))','URL',tweet)

    #Convert @username to AT_USER
    tweet = re.sub('@[^\s]+','AT_USER',tweet)

    #Remove additional white spaces
    tweet = re.sub('[\s]+', ' ', tweet)

    #Replace #word with word
    tweet = re.sub(r'#([^\s]+)', r'\1', tweet)
	
	#Remove 'and'
    tweet = tweet.replace(' and ', ' ')

    #trim
    tweet = tweet.strip('\'"')
	
	#remove punctuation
    exclude = set(string.punctuation)
    #exclude.add('&amp;')
    s=""
    #s= ''.join(ch for ch in tweet if ch not in exclude)
    for ch in tweet:
		if ch not in exclude:
			s=s+ch
		else:
			s=s+' '

    return s
#end

 
#start getStopWordList
def getStopWordList(stopWordListFileName):
    #read the stopwords file and build a list
    stopWords = []
    stopWords.append('AT_USER')
    stopWords.append('URL')
 
    fp = open(stopWordListFileName, 'r')
    line = fp.readline()
    while line:
        word = line.strip()
        stopWords.append(word)
        line = fp.readline()
    fp.close()
    return stopWords
#end


#start getFeatureWordList
def getFeatureWordList():
	unigrams=[]
	bigrams=[]
   
	# prepare a cursor object using cursor() method
	cursor = db.cursor()

	# Get unigrams
	sql = "Select * from unigrams"
	
	try:
   # Execute the SQL command
		cursor.execute(sql)
	# Fetch all the unigrams and pos neg weight.
		results = cursor.fetchall()
		for row in results:
			
			word = [row[0],row[1],row[2],row[3]]
			unigrams.append(word)
   
	except:
		print "Error: unable to fetch data"

	finally:
		cursor.close() #close anyway
		
	# prepare a cursor object using cursor() method
	cursor1 = db.cursor()
	
	# Get bigrams
	sql1 = "Select * from bigrams"
	
	try:
   # Execute the SQL command
		cursor1.execute(sql1)
		
	# Fetch all the rows in a list of lists.
		results = cursor1.fetchall()
		
		for row in results:
			#print row[0]
			word = [row[0],row[1],row[2]]
			bigrams.append(word)
   
	except:
		print "Error: unable to fetch data"

	finally:
		cursor1.close() #close anyway
	'''
	#read the feature words file and build a list
    #featureWords = []
   
	
    fp = open(featureWordListFileName, 'r')
    line = fp.readline()
    
	#read unigrams
	
    while line!='\n':
        line = line.split(' ')
        word = [line[0],line[1],line[2][:-1]]
		
        unigrams.append(word)
        line = fp.readline()

    line = fp.readline()
	
	#read bigrams
    while line and line!='\n':
        line = line.split(' ')
        word = [line[0] + ' ' + line[1],line[2],line[3][:-1]]
        bigrams.append(word)
        line = fp.readline()
		
    fp.close()
	'''
   # print featureWords
	return unigrams,bigrams
#end

   
#start getfeatureVector
def getFeatureVector(tweet):
    featureVector = []
    #split tweet into words
    words = tweet.split()
    for w in words:
        #strip punctuation
        w = w.strip('\'"?,.')
		
        #check if the word starts with an alphabet
        val = re.search(r"^[a-zA-Z][a-zA-Z0-9]*$", w)
		
        #ignore if it is a stop word
        if(w in stopWords or val is None):
            continue
        else:
            featureVector.append(w.lower())
			
    return featureVector
#end


#sentiment analyzer
def get_sentiment(processedTweet):
	featureVector = getFeatureVector(processedTweet)
	existing_features = []
	new_features = []
	removeList = []
	processedVector= processedTweet.split()
	for i in range(len(processedVector)-1):
		curr_bigram = processedVector[i] + ' ' + processedVector[i+1]
		for b in bigrams:
			if curr_bigram==b[0]:
				existing_features.append(b)
				removeList.append(processedVector[i])
				removeList.append(processedVector[i+1])
				

	flag=0
	for f in featureVector:
		for feature in unigrams:
			if feature[0] not in removeList:
				if f==feature[0]:
					existing_features.append(feature)
					flag=1
					break
			else:
				flag=1
			
		if flag==0:
			new_features.append(f)
			# remove company names!!!!!!!!!!!!!!!!!!!!!!
		'''if feature in featureWords:
			existing_features.append(feature)
		else:
			new_features.append(feature)
			'''
	
	#fo.write(str(count) + '. Tweet: ' + processedTweet + '\n')
	#count=count+1
	#fo.write('existing features: ')
	'''
	for e in existing_features:
		fo.write(e[0] + '   ')
	if existing_features==[]:
		fo.write('no features\n')
	fo.write('\n')
	'''
	flag=0
	pos_prob=0.0
	neg_prob=0.0
	sen=''
	if existing_features:
		#fo.write('existing features: ' + str(existing_features) + '\n') 
		flag=1
	
		#print 'Tweet: ' + processedTweet
		#print 'existing features: ' , existing_features , '\n'
		
		# sentiment analysis part
		
		
		for f in existing_features:
			pos_prob = pos_prob + math.log(float(f[1]))
			neg_prob = neg_prob + math.log(float(f[2]))
			
		if pos_prob>neg_prob:
			sen = 'b'
			#print "Buy\n"
			#fo.write('Buy \n\n')
		else:
			sen = 's'
			#print "Sell\n"
			#fo.write('Sell \n \n')
			
		#print pos_prob, neg_prob
	return flag,pos_prob,neg_prob,sen,new_features	
#end

#initialize stopWords and featureWords
stopWords = []
featureWords = []
stopWords = getStopWordList('data/stopwords.txt')
unigrams,bigrams= getFeatureWordList()
#print unigrams,bigrams
global_new_features = []


# prepare a cursor object using cursor() method
cursor = db.cursor()

# Get tweets and analyse sentiment
#sql = "Select tweet_id, tweet_text from tweets"
sql = "Select tweet_id, tweet_text from tweets where is_processed=0"

try:
   # Execute the SQL command
   cursor.execute(sql)
   # Fetch all the rows in a list of lists.
   results = cursor.fetchall()
   
except:
	print "Error: unable to fetch data"

finally:
    cursor.close() #close anyway

fo = open('data/output.txt', 'w')	

for row in results:
	tweet_id = row[0]
	tweet = row[1]
	processedTweet = processTweet(tweet)
	fo.write('Tweet: ' + processedTweet + '\n')	
	print processedTweet
	
	query = "SELECT tag_id, keyword FROM tweets AS t \
	JOIN tweet_tags AS tt ON t.tweet_id = tt.tweet_id \
	WHERE tt.tweet_id = '%d'" % (tweet_id)
	
	cursor = db.cursor()
	
	try:
		# Execute the SQL command
		cursor.execute(query)
		# Fetch all the rows in a list of lists.
		res = cursor.fetchall()
   
	except:
		print "Error: unable to fetch data"

	finally:
		cursor.close() #close anyway
	count=0
	company_arr={}
	
	for row in res:	
		tag_id = row[0]
		company = row[1]
		
		if company.find(' ') !=-1 :
			start=processedTweet.find(company)
			end=start+len(company)
			company=company.replace(" ","")
			processedTweet=processedTweet[:start]+company+processedTweet[end:]
		company_arr[company]=tag_id;	
			
		#fo.write(company + '   ')
		count=count+1
	#fo.write(str(count) + str(company_arr))
	#fo.write('\n')
	
	if count==1:
		flag,pos_prob,neg_prob,sen,new_features	= get_sentiment(processedTweet)
		if new_features!=[]:
			global_new_features.extend(new_features)
		if flag==1:
			fo.write(sen+'\n\n')
			
			sql2 = "update tweet_tags set pos_prob='%f', neg_prob='%f', sentiment='%c' where tweet_id='%d'" % (pos_prob, neg_prob, sen, tweet_id)
			sql3 = "update tweets set is_processed='1' where tweet_id='%d'" % (tweet_id)
			
			try:
				cursor2 = db.cursor()
				cursor2.execute(sql2)
				cursor3 = db.cursor()
				cursor3.execute(sql3)
				db.commit()
			except:
				print "cannot update DB"
				db.rollback()
			finally:
				cursor2.close() # close anyway
				cursor3.close() # close anyway
			
	else:
		current_str=''
		global_list=[]
		company_seen=False
		tweet_words=processedTweet.split()
		for word in tweet_words:
			if word in company_arr:
				if company_seen == False:
					#get sentiment of current segment 
					flag,pos_prob,neg_prob,sen,new_features	= get_sentiment(current_str)
					if new_features!=[]:
						global_new_features.extend(new_features)
					global_list.append(['sent',flag,pos_prob,neg_prob,sen])
					current_str = word
					company_seen = True
				else:
					current_str = current_str + ' ' + word
			else:
				if company_seen == True:
					# append tags of company
					comp_list = current_str.split()
					clist=[]
					for comp in comp_list:
						clist.append([comp,company_arr[comp]])
					global_list.append(clist)
					current_str = word
					company_seen = False
				else:
					current_str = current_str + ' ' + word
		if company_seen==False:
			flag,pos_prob,neg_prob,sen,new_features	= get_sentiment(current_str)
			if new_features!=[]:
				global_new_features.extend(new_features)
			global_list.append(['sent',flag,pos_prob,neg_prob,sen])
		else :
			comp_list = current_str.split()
			clist=[]
			for comp in comp_list:
				clist.append([comp,company_arr[comp]])
			global_list.append(clist)
		#fo.write(str(global_list) + '\n\n')
		
		i=0
		j=len(global_list)
		#sent_seen = False
		#comp_seen = False
		while i<j:
			if global_list[i][0] == 'sent':
				if global_list[i][1] == 1:
					pos_prob = global_list[i][2]
					neg_prob = global_list[i][3]
					sen = global_list[i][4]
					i = i+1
					if i==j:
						continue
					for c in global_list[i]:
						sql2 = "update tweet_tags set pos_prob='%f', neg_prob='%f', sentiment='%c' where tweet_id='%d' and tag_id='%d'" % (pos_prob, neg_prob, sen, tweet_id, c[1])
						fo.write(c[0] + ':' + sen + '\n')
						try:
							cursor2 = db.cursor()
							cursor2.execute(sql2)
							db.commit()
						except:
							print "cannot update DB"
							db.rollback()
						finally:
							cursor2.close() # close anyway
					
					sql3 = "update tweets set is_processed='1' where tweet_id='%d'" % (tweet_id)
					try:
						cursor3 = db.cursor()
						cursor3.execute(sql3)
						db.commit()
					except:
						print "cannot update DB"
						db.rollback()
					finally:
						cursor3.close() # close anyway
					i = i+1
					
				else:
					i = i+1
			else:
				k = i+1
				if k<j and global_list[k][1] == 1:
					pos_prob = global_list[k][2]
					neg_prob = global_list[k][3]
					sen = global_list[k][4]
					
					for c in global_list[i]:
						sql2 = "update tweet_tags set pos_prob='%f', neg_prob='%f', sentiment='%c' where tweet_id='%d' and tag_id='%d'" % (pos_prob, neg_prob, sen, tweet_id, c[1])					
						fo.write(c[0] + ':' + sen + '\n')
						try:
							cursor2 = db.cursor()
							cursor2.execute(sql2)
							db.commit()
						except:
							print "cannot update DB"
							db.rollback()
						finally:
							cursor2.close() # close anyway

					sql3 = "update tweets set is_processed='1' where tweet_id='%d'" % (tweet_id)
					try:
						cursor3 = db.cursor()
						cursor3.execute(sql3)
						db.commit()
					except:
						print "cannot update DB"
						db.rollback()
					finally:
						cursor3.close() # close anyway
				i = i+2
				fo.write('\n')
				
				

				
#print global_new_features
#print 'number of new feature words ', len(global_new_features)
fo.close()


fi = open('data/new_words.txt', 'w')

#process new feature words
#lmtzr = WordNetLemmatizer()
all_syn = []
for new_word in global_new_features:
	#base_word = lmtzr.lemmatize(new_word)
	#base_word = wn.morphy('buzzing', wn.VERB)
	base_word = new_word
	if base_word:
		syns = wn.synsets(base_word)
		#valid_syn = []
		count_valid_syn = 0
		p = 0.0
		n = 0.0
		part_of_speech = ''
		for s in syns:
			i = s.name.find('.')
			name = s.name[:i]
			for feature in unigrams:
				if name == feature[0] and s.pos == feature[3]:
					p = p +feature[1]
					n = n + feature[2]
					part_of_speech = feature[3]
					count_valid_syn = count_valid_syn + 1
					fi.write(str([base_word, feature[0], feature[1], feature[2]]) + '\n')				
		if count_valid_syn > 0:
			p = p/2.0/count_valid_syn
			n = n/2.0/count_valid_syn
			valid_syn = [base_word, p, n, part_of_speech]
			fi.write('match found: ' + str(valid_syn) + '\n')
			all_syn.append(valid_syn)
						
#print all_syn
#fi.write(str(all_syn) + '\n')
fi.close()


for new_word in all_syn:
		
	try:
		sql = "insert into unigrams(feature, pos, neg, part_of_speech ) values('%s', '%f', '%f', '%c')" % ( new_word[0], new_word[1], new_word[2], new_word[3])
		#print new_word[0], new_word[1], new_word[2], new_word[3]
		#print sql
		cursor = db.cursor()
		cursor.execute(sql)
		print "success"
		db.commit()
	except:
		#print "cannot update DB, maybe duplicate!"
		db.rollback()
	finally:
		cursor.close() # close anyway



# disconnect from server
db.close()
