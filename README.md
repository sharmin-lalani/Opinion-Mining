# Opinion-Mining
Sentiment analysis and aggregation of stock market preditcions

# Problem Statement
Stock market experts often use blogs or social networking platforms to express their views about the current trends in the market. The people looking to invest in any particular stock search the web for predictions by experts on that particular stock to gauge the overall opinion. It is practically impossible for any other user, naive or expert, to go through all the opinions expressed on distributed across a site. This project aims to present market predictions in various sectors, collected from twitter, in a consolidated manner.

# Methodology and Resources
First phase would be collecting stock market opinions of expert market predictors from twitter. We are using the twitter PHP API and PHP libraries for implementing this phase. In the Second phase, we would process the data to bring it into the pre-defined format so that the data could be filtered and analyzed. Second phase is mapping of opinions (tweets) to company names and sector names. This phase also involves discarding of irrelevant tweets. We are using PHP for filtering of tweets.

The third phase consists of analyzing each individual opinion and extracting the buy or sell sentiment from it. It is possible that some tweets do not express such a sentiment and hence they will not be considered during consolidation. We are using python for sentiment analysis. The next phase is aggregation or consolidation. As the opinions could be from people with different levels of expertise, we cannot assign the same weight to each opinion. We need to distinguish the predictors to decide their credibility. Depending on the favorite count of an opinion or the number of re-tweets for an opinion we will assign appropriate weights to their predictions. The last phase is a pictorial presentation of the aggregated opinion about a particular stock or sector using web-based graphs and charts.

# Target Output
The user can submit a query for any company or a particular sector. The output would be represented in the pictorial form showing the current company status or the stock status as well as the trends over a period of time. An aggregate opinion for each stock market predictor can also be shown. The person can then decide on the basis of the output whether he or she should invest in a particular company or sector.

# Tools and Technologies
PHP, Python, MySQL, Javascript API(Highstocks and Highcharts)
