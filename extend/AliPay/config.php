<?php

$config = array (
    //应用ID,您的APPID。
    'app_id' => "2018101361694139",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpAIBAAKCAQEAwRQWx+FYZqIHISoUFYPn23paT5J3H3Bv4g3qhVSPejB6G/3i
2vFmuP3cXqW1+rznom0qmhfXB/59iplBAqlOtJGIAQnEyou/5fx8RYyfE8YELNwD
nznipF0uO+LJkasXdreSGfdEZn13WVBUoQvbNqp6MeD2OR7bm271GfWGclIak8Mg
9dbsR9O1uOX57PkYzftW3AXJGW+/4pecmVcTTZ/j8Wj3COwkr8C3qJcfuodZdYCr
YhcJ16WFGFqUxzWORFu+Jha8dbFeZQoNMvg3E94QifiuMFAzvAbiPDUK+qf3Izu2
hbKcdoTRUpCUqyReE0xdDGLb32zUnOlPo4CSawIDAQABAoIBAC6atcD7cMYdOBFW
QdDLrQx6JRBt7xkEn8z1xPT360X4K5FUIAzvFfdzN10VLK7gKB05sn3NYVcJo63v
vhRxX5EQS77MZ9boEqLl5e03uzIfquRVGv61DzLTVxSXckYaASjjuH1SBQ2kHUnO
rQ7OeNRiI+qkPWqeOy3yg3EHpbM0F+j86g5bWKwPXP9BwIlX7YXtsN9ckn2njdgt
2vyj/5yk3iuJqMMLMy61Xs/522wxDtBVM0hX9mJqkn5UmGYeyNmbWmuC/H69Qvea
p7g7r4Z81MShWOWpzVgggbNKRH0jxk/gc8DzwATxHWGpTL7JvdANwOe2IBexe6mw
e+2DOxkCgYEA/sAqbPKGft3Nr6Qb5dhHi5lvO40TB8vfkRCnyxrvN4Sbi6SbgdDE
K/wdUNf/x1dpQUlRxUuvgpxoO05MUKlWdx1tcWNM0QefWaJuIoaQg3CDD26bAHnO
gF8Vi6vNqRRFZD3yoN7JPJ17qS1eRtdKj8196wSADAuw6XUNQ6nykC0CgYEAwgZ+
vqxW/0xHSgYdibKmJQmDVrMMrskvWfwQ8UDMBxSqQbv3g0mJkWP1x/0RT4Sf+stD
NvpWyH3ZgUAPrarSsdb3jl6HLjjQ2qdtd05f+Fk/V+OlT/8f/LS0uBpM4M0N5YxY
3no0sN2ACEz2pYnSUceV4uJO80VcvXv6Dm9Is/cCgYEAsfbe7IM0r/YVVclIb/X6
bSyAB1MTY1PqV7YR8hJb4SXzX8dbl4GXOb8DX9G1brXC4wfsZu7rqkvHiP6203ao
38egCCE9rZ0slfqiA96LoDb+Z8513Aw71XQXYh5gKujtXQzlrbpVTNIbbGE0Rjrq
bLE6cO8Q4Nld+ol5/bYArPUCgYB8xbKCKc/0c3gwyUf7ghBIJxr4CUKxsB0sT+YR
TyslrotipF8iZYr7HX40MrEMIuzCWi6KT2i5m/zgmnK6quy9gtFGE9H4zn647gx+
Ob+LtNEAUxu/GwDWANtqvTcNJbC3Go8X2xzBVF/+dofyonPHNYNVGVXOKZ/fFrgS
ykvFtwKBgQDxrBF9PNt5RfNuqHsMBx6j1o2tNR6VtErgJ/ribEFef1tSCce5ijoN
OPRn+/eIAa8Dv6slF76rF4LmZlngJjKQcV+ztL78ouVx5Pdbt8PeznZV82YZzxSt
ihjWSdZpFDsidZkpe+RqwTUaVYpP0t69GbRWjpO/IoCX6qZsDxXVGw==",

    //异步通知地址
    'notify_url' => "http://localhost/automobile/public/admin/goods_pay_code",

    //同步跳转
    'return_url' => "http://localhost/automobile/public/admin/goods_pay_code",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwRQWx+FYZqIHISoUFYPn
23paT5J3H3Bv4g3qhVSPejB6G/3i2vFmuP3cXqW1+rznom0qmhfXB/59iplBAqlO
tJGIAQnEyou/5fx8RYyfE8YELNwDnznipF0uO+LJkasXdreSGfdEZn13WVBUoQvb
Nqp6MeD2OR7bm271GfWGclIak8Mg9dbsR9O1uOX57PkYzftW3AXJGW+/4pecmVcT
TZ/j8Wj3COwkr8C3qJcfuodZdYCrYhcJ16WFGFqUxzWORFu+Jha8dbFeZQoNMvg3
E94QifiuMFAzvAbiPDUK+qf3Izu2hbKcdoTRUpCUqyReE0xdDGLb32zUnOlPo4CS
awIDAQAB",


);


$con = array (
    //应用ID,您的APPID。
    'app_id' => "2018101361694139",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpAIBAAKCAQEAwRQWx+FYZqIHISoUFYPn23paT5J3H3Bv4g3qhVSPejB6G/3i
2vFmuP3cXqW1+rznom0qmhfXB/59iplBAqlOtJGIAQnEyou/5fx8RYyfE8YELNwD
nznipF0uO+LJkasXdreSGfdEZn13WVBUoQvbNqp6MeD2OR7bm271GfWGclIak8Mg
9dbsR9O1uOX57PkYzftW3AXJGW+/4pecmVcTTZ/j8Wj3COwkr8C3qJcfuodZdYCr
YhcJ16WFGFqUxzWORFu+Jha8dbFeZQoNMvg3E94QifiuMFAzvAbiPDUK+qf3Izu2
hbKcdoTRUpCUqyReE0xdDGLb32zUnOlPo4CSawIDAQABAoIBAC6atcD7cMYdOBFW
QdDLrQx6JRBt7xkEn8z1xPT360X4K5FUIAzvFfdzN10VLK7gKB05sn3NYVcJo63v
vhRxX5EQS77MZ9boEqLl5e03uzIfquRVGv61DzLTVxSXckYaASjjuH1SBQ2kHUnO
rQ7OeNRiI+qkPWqeOy3yg3EHpbM0F+j86g5bWKwPXP9BwIlX7YXtsN9ckn2njdgt
2vyj/5yk3iuJqMMLMy61Xs/522wxDtBVM0hX9mJqkn5UmGYeyNmbWmuC/H69Qvea
p7g7r4Z81MShWOWpzVgggbNKRH0jxk/gc8DzwATxHWGpTL7JvdANwOe2IBexe6mw
e+2DOxkCgYEA/sAqbPKGft3Nr6Qb5dhHi5lvO40TB8vfkRCnyxrvN4Sbi6SbgdDE
K/wdUNf/x1dpQUlRxUuvgpxoO05MUKlWdx1tcWNM0QefWaJuIoaQg3CDD26bAHnO
gF8Vi6vNqRRFZD3yoN7JPJ17qS1eRtdKj8196wSADAuw6XUNQ6nykC0CgYEAwgZ+
vqxW/0xHSgYdibKmJQmDVrMMrskvWfwQ8UDMBxSqQbv3g0mJkWP1x/0RT4Sf+stD
NvpWyH3ZgUAPrarSsdb3jl6HLjjQ2qdtd05f+Fk/V+OlT/8f/LS0uBpM4M0N5YxY
3no0sN2ACEz2pYnSUceV4uJO80VcvXv6Dm9Is/cCgYEAsfbe7IM0r/YVVclIb/X6
bSyAB1MTY1PqV7YR8hJb4SXzX8dbl4GXOb8DX9G1brXC4wfsZu7rqkvHiP6203ao
38egCCE9rZ0slfqiA96LoDb+Z8513Aw71XQXYh5gKujtXQzlrbpVTNIbbGE0Rjrq
bLE6cO8Q4Nld+ol5/bYArPUCgYB8xbKCKc/0c3gwyUf7ghBIJxr4CUKxsB0sT+YR
TyslrotipF8iZYr7HX40MrEMIuzCWi6KT2i5m/zgmnK6quy9gtFGE9H4zn647gx+
Ob+LtNEAUxu/GwDWANtqvTcNJbC3Go8X2xzBVF/+dofyonPHNYNVGVXOKZ/fFrgS
ykvFtwKBgQDxrBF9PNt5RfNuqHsMBx6j1o2tNR6VtErgJ/ribEFef1tSCce5ijoN
OPRn+/eIAa8Dv6slF76rF4LmZlngJjKQcV+ztL78ouVx5Pdbt8PeznZV82YZzxSt
ihjWSdZpFDsidZkpe+RqwTUaVYpP0t69GbRWjpO/IoCX6qZsDxXVGw==",

    //异步通知地址
    'notify_url' => "https://automobile.siring.com.cn/index_pay_code",

    //同步跳转
    'return_url' => "https://automobile.siring.com.cn/index_pay_code",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwRQWx+FYZqIHISoUFYPn
23paT5J3H3Bv4g3qhVSPejB6G/3i2vFmuP3cXqW1+rznom0qmhfXB/59iplBAqlO
tJGIAQnEyou/5fx8RYyfE8YELNwDnznipF0uO+LJkasXdreSGfdEZn13WVBUoQvb
Nqp6MeD2OR7bm271GfWGclIak8Mg9dbsR9O1uOX57PkYzftW3AXJGW+/4pecmVcT
TZ/j8Wj3COwkr8C3qJcfuodZdYCrYhcJ16WFGFqUxzWORFu+Jha8dbFeZQoNMvg3
E94QifiuMFAzvAbiPDUK+qf3Izu2hbKcdoTRUpCUqyReE0xdDGLb32zUnOlPo4CS
awIDAQAB",


);