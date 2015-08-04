look elastica configuration:
```bash
app/console de:conf fos_elastica
```
reindex:
```bash
app/console f:e:p
```

*place where convert php query to elastica request:*
**\Elastica\Search::search**
**vendor/ruflin/elastica/lib/Elastica/Search.php**
or
**\Elastica\Transport\Http::exec**
**vendor/ruflin/elastica/lib/Elastica/Transport/Http.php**

*main elastica features:*
**https://www.elastic.co/guide/en/elasticsearch/reference/current/common-options.html**
`pretty`
`filter_path`

*standard query:*
`http://localhost:9200`  - default host
`sdelka_advert_dev/_search` - index_name (not indexes for php application, but elastica)
`pretty=1` - pretty format
or
`sdelka_advert_dev/advert/_search` - index_name/type
simple field query:
`http://localhost:9200/sdelka_advert_dev/advert/_search?q=title:значение`
or
**https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-body.html**

*get mappings:*
`sdelka_advert_dev/_mapping/advert`
or
`curl -XGET 'http://localhost:9200/_mapping'`
*get mapping for a field:*
`curl -XGET 'http://localhost:9200/sdelka_advert_dev/_mapping/advert/field/FIELDNAME'`

*validate query:*
`/sdelka_advert_dev/_validate/query?explain`

*explain score in query* (first run query, look at result, find $ID; Method: GET!):
`sdelka_advert_dev/advert/$ID/_explain?q=`....

*test tokenizer:*
`http://localhost:9200/_analyze?tokenizer=whitespace&text=asdf`
`http://localhost:9200/_analyze?tokenizer=whitespace&filters=lowercase&text=asdf`

*test analyzer:*
`http://localhost:9200/sdelka_advert_dev/_analyze?field=description&text=asdf`
