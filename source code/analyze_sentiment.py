import re
import math

#initialize stopWords and featureWords
stopWords = []
featureWords = []
 
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
def getFeatureWordList(featureWordListFileName):
    #read the feature words file and build a list
    #featureWords = []
    unigrams=[]
    bigrams=[]
	
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
 
 
#Read the tweets one by one and process it
fp = open('data/processed_tweets.txt', 'r')

processedTweet = fp.readline()
stopWords = getStopWordList('data/stopwords.txt')
unigrams,bigrams= getFeatureWordList('data/feature_list.txt')
print unigrams,bigrams

fo = open('data/output.txt', 'w')

while processedTweet:
	featureVector = getFeatureVector(processedTweet)
	existing_features = []
	new_features = []
	removeList = []
	processedVector= processedTweet.split(' ')
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
		'''if feature in featureWords:
			existing_features.append(feature)
		else:
			new_features.append(feature)
			'''
	if existing_features:
	
		print 'Tweet: ' + processedTweet
		fo.write('Tweet: ' + processedTweet + '\n')
		print 'existing features: ' , existing_features , '\n'
		
		# sentiment analysis part
		pos_prob=0.0
		neg_prob=0.0
		
		for f in existing_features:
			pos_prob = pos_prob + math.log(float(f[1]))
			neg_prob = neg_prob + math.log(float(f[2]))
			
		if pos_prob>neg_prob:
			print "Buy\n"
			fo.write('Buy \n\n')
		else:
			print "Sell\n"
			fo.write('Sell \n \n')
		
	 
	processedTweet = fp.readline()
#end loop




fp.close()