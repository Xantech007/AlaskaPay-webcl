== Table structure for table admin

|------
|Column|Type|Null|Default
|------
|//**id**//|int(11)|No|
|**username**|varchar(100)|No|
|full_name|varchar(255)|No|
|password|varchar(255)|No|
|created_at|timestamp|Yes|current_timestamp()
== Table structure for table contributions

|------
|Column|Type|Null|Default
|------
|//**id**//|int(11)|No|
|user_id|int(11)|No|
|amount|decimal(12,2)|No|
|duration|int(11)|No|
|status|enum('pending', 'active', 'inactive', 'completed')|Yes|pending
|reward_rate|decimal(5,2)|Yes|0.00
|created_at|timestamp|Yes|current_timestamp()
== Table structure for table loans

|------
|Column|Type|Null|Default
|------
|//**id**//|int(11)|No|
|user_id|int(11)|No|
|amount|decimal(15,2)|No|
|term|int(11)|No|
|purpose|enum('personal', 'business', 'education', 'medical', 'home', 'other')|No|
|status|enum('pending', 'approved', 'rejected', 'paid')|Yes|pending
|approved_at|datetime|Yes|NULL
|paid_at|datetime|Yes|NULL
|created_at|timestamp|Yes|current_timestamp()
== Table structure for table transactions

|------
|Column|Type|Null|Default
|------
|//**id**//|int(11)|No|
|user_id|int(11)|No|
|type|enum('deposit', 'withdraw', 'transfer', 'credit', 'debit')|No|
|amount|decimal(15,2)|No|0.00
|reference|varchar(100)|Yes|NULL
|status|enum('pending', 'completed', 'failed')|Yes|completed
|created_at|timestamp|Yes|current_timestamp()
== Table structure for table users

|------
|Column|Type|Null|Default
|------
|//**id**//|int(11)|No|
|**username**|varchar(100)|No|
|**email**|varchar(255)|No|
|password|varchar(255)|No|
|full_name|varchar(255)|Yes|NULL
|phone|varchar(50)|Yes|NULL
|balance|decimal(15,2)|Yes|0.00
|is_verified|tinyint(1)|Yes|0
|loan_min|decimal(12,2)|Yes|500.00
|loan_max|decimal(12,2)|Yes|5000.00
|contribution_level|enum('Basic', 'Standard', 'Premium')|Yes|Basic
|reward_rate|decimal(10,2)|Yes|0.00
|reward_min|decimal(12,2)|Yes|25.00
|reward_max|decimal(12,2)|Yes|5000.00
|status|enum('active', 'suspended')|Yes|active
|country|varchar(100)|Yes|NULL
|state|varchar(100)|Yes|NULL
|created_at|timestamp|Yes|current_timestamp()
|verification_method|varchar(100)|Yes|NULL
|id_number|varchar(100)|Yes|NULL
|verified_at|datetime|Yes|NULL
