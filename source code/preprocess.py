#import regex
import re
 
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

    #trim
    tweet = tweet.strip('\'"')

    return tweet
#end
 
#Read the tweets one by one and process it
fr = open('data/tweets.txt', 'r')
fw = open('data/processed_tweets.txt', 'w')
line = fr.readline()
 
while line:
    processedTweet = processTweet(line)
    print processedTweet
    fw.write(processedTweet + '\n')
    line = fr.readline()
#end loop

fr.close()
fw.close()