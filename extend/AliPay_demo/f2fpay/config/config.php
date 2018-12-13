<?php
$config = array (
		//签名方式,默认为RSA2(RSA2048)
		'sign_type' => "RSA2",

		//支付宝公钥
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyUgwfM85daNJ3tHlmsFET1Mcc6kaXjLD9AdjOy8pknzHVvDp66fMp2TZfnJjwGUlYDr9w/mY9CDbzwQgwllVUCSNxzVLH57FfRuzVStoHN22BJBMTvkMPpSbEvV3MxEzXzrl2kyV5sqEEyDiY28wpX1aFb5ug/G18R6216DMqGlTdYwHSOBd+S+2r2B5ljk786dtRf0inimEAGP5CdX2HxI8svpUVq2OeabSZ/kvf39yW9oLNLz0WIAwHQiWSg3DCb+MBP4I/W+5pX6N8p+ijqtPDJTF7/XBDQnftKDfVwoFL/yOxc+XP3pFbU3h5F7Srv3FPoO1Cnh38P5iiY/pFwIDAQAB",

		//商户私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEAv/Ops+YlhYbxe1cPgil7t2Hn0ceNVlK89KE2t2ZXYfUQNanfL+aQtbR/r7cR0VAD4CM6NOL7nnkOxTxXyEXLFrgsje/ifPsZR9HTM8HPzKb0mIWMY46CE+zkGgwfuofdgOF2ccqHXMv99vzZc6YMuiZoEGdF5TOZ1a+XgK4XIcoWK+eJLCVfP6bNh3iFgwqv/27iEfSaF71InUYiKnOm8lFh6TETeMQeMv+ND02IAwWEeWdzmfm63YgN2RDHRtRJRjtBifYR3TXV5jXdS6rFt/FIp72eWpCaWVeJPlmoFGqCHcLF7aFhtD3OGMpHTGkxSyjsdGqpdEgyEpuFr54ClQIDAQABAoIBAQCmuCIbiLfqRAyVzkZADXvRCY5p3GLcKTzJVQTLQm5/PHT5xzN8zUu/JqkIm380R8U19iLmMIfPWuIDWaf84PHGJ+060rHrT8TjZlGE0L+FxzUHDGelSO+eEtED9qfQQZWYDTvtFbWAWgWZcoAEPKMtQ5J7PNeufaUNvldegtbAltyNR9a4GRtDNkJP8gd3JA4W4F9MQqxDC55v9pKo+2JmR4/bFS/p6p+xVFMRFGEWgWctQnqWuMOIYox1KG7xrA0yugZ4BW7EberI6s6ULMvIvnHHBFH4YrT829eZE/mjbfSKEuxwnWvwH/AD8Se+Zi0sQzUkz4XgiL/KxmBS5K15AoGBAPXLizXhCtBtyJphmrl4JJO0p715wrl7QKfT7sxIoMKIeSZyPjYf9XBCE0q5787vjWhxIP/hLBttF3gR2GaQlXc0mp+If6q/77Wbihmn79ubpqUIfsqZ8v37e2trA483HpZ0FFBsL5wY6l+4EVI6oD65cbHdWpPRQsL9DK6Ef2Z/AoGBAMfr1ziIw33PK/PjBlbO/KVXVR/g32bsIIPX3PfH6ODTeGkS/oIk3oDFBPT7LZdZx2+OAstKHdZEIZbZkxSdn0tlTyu+CcdJeJu5HEp+YLAtY7txTwgCQaRkVRN36lrH33PspBOlCY5am+/0+lnEkwIC4I2xUMfgdi6MhZ/61xTrAoGBAK/RlFfLDTBAh3EfBk1LCmb5n88iLM7+wiYKrFNGVHAyrO56Y9PVknxlV2VpkYdlxbIC0kcWq7VGmLYTlcbv/2pQn5YkTqY2O2KdyvBFN4bQVSjR5dhIvQ8+d29EEjy8gc2/ZNSOPpQbInOruAAo3X1gq1o+a083LdlKp2qibntDAoGAGZjJopRoK0iGQSC1JBI2aNkIrh81m1DqTNcrlJIE8BxDU3i6G2GrF2OiYddzG+jDzzWoh+zCazen8U+ENGSGC7o568jWKazvg0itJaCOsp/tIAe+7bCURP8PoYF+AQeMRaRr0ZQU6CQTySJdl3bCt8Q0ig/E3wKPPyCAz13ngecCgYBgAZE9OOG1K51mS4IxWzi2XAOP6VaQG1s9lMgaOkSRBlscKzF86YOzo16nw8nM7jHaOeGDe2QzO268fTclbGUSm6BwJOseSp+/8HYx0M6W8wNRXjU60XuoGfaLG8yBIjXFz62cBCiWd4+wKOKsCMKcgs249hP9AL7X0smoPEsGpQ==",

		//编码格式
		'charset' => "UTF-8",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//应用ID
		'app_id' => "2018120762470526",

		//异步通知地址,只有扫码支付预下单可用
		'notify_url' => "https://automobile.siring.com.cn/index_pay_code",

		//最大查询重试次数
		'MaxQueryRetry' => "10",

		//查询间隔
		'QueryDuration' => "3"
);