# Investment & Rank Check Seeder

## বিবরণ
এই seeder ফাইলটি investment এবং rank upgrade সিস্টেম পরীক্ষা করার জন্য তৈরি করা হয়েছে। এটি টেস্ট ডেটা তৈরি করে এবং rank upgrade functionality চেক করে।

## কি করে এই Seeder?

### 1. Rank Requirements সিড করা
- যদি আগে থেকে rank requirements না থাকে তাহলে ডিফল্ট requirements সিড করে
- 12টি rank level এর জন্য requirements তৈরি করে

### 2. টেস্ট ইউজার তৈরি করা
- **Parent User**: `parent@test.com` - একজন মূল ইউজার
- **Child Users**: 5জন child user যারা parent এর referral
- প্রতিটি user এর balance, rank এবং অন্যান্য তথ্য সেট করা হয়

### 3. টেস্ট Investment তৈরি করা
- Parent user এর জন্য 1000 টাকার investment
- প্রতিটি child user এর জন্য 500-900 টাকার investment
- সব investment active status এ রাখা হয়

### 4. Rank Upgrade চেক করা
- **Team Business Volume**: সব downline user এর total investment
- **Count Level**: Team depth (কত level পর্যন্ত team আছে)
- **Personal Investment**: নিজের total investment
- **Rank Eligibility**: পরবর্তী rank এর জন্য eligible কিনা
- **Automatic Upgrade**: যদি eligible হয় তাহলে automatic rank upgrade

## কিভাবে চালাবেন?

```bash
php artisan db:seed --class=InvestmentRankSeeder
```

## আউটপুট কি দেখাবে?

1. **Rank Requirements Status**: আগে থেকে আছে কিনা
2. **Test Users Creation**: কতজন user তৈরি হলো
3. **Test Investments Creation**: কতটি investment তৈরি হলো
4. **Parent User Analysis**:
   - Current rank
   - Team business volume
   - Count level (team depth)
   - Personal investment
   - Eligibility status
   - Rank upgrade result
5. **All Rank Requirements**: প্রতিটি rank এর requirements এবং eligibility status
6. **Child Users Analysis**: প্রতিটি child user এর rank information

## Rank Requirements (ডিফল্ট)

| Rank | Team Volume | Count Level | Personal Investment |
|------|-------------|-------------|--------------------|
| 1    | 12,000      | 6           | 100                |
| 2    | 25,000      | 8           | 250                |
| 3    | 50,000      | 10          | 500                |
| 4    | 100,000     | 12          | 1,000              |
| 5    | 200,000     | 14          | 2,000              |
| 6    | 350,000     | 16          | 3,500              |
| 7    | 500,000     | 18          | 5,000              |
| 8    | 750,000     | 20          | 7,500              |
| 9    | 1,000,000   | 22          | 10,000             |
| 10   | 1,500,000   | 24          | 15,000             |
| 11   | 2,000,000   | 26          | 20,000             |
| 12   | 3,000,000   | 28          | 30,000             |

## টেস্ট ডেটা বিবরণ

### Parent User
- Email: parent@test.com
- Balance: 10,000 টাকা
- Investment: 1,000 টাকা
- 5জন direct referral

### Child Users
- Email: child1@test.com থেকে child5@test.com
- Balance: 5,000 টাকা করে
- Investment: 500-900 টাকা (প্রতিজনের আলাদা)
- সবাই parent এর referral

## ব্যবহারের ক্ষেত্র

1. **Development Testing**: নতুন rank upgrade feature test করার জন্য
2. **Data Analysis**: Rank system কিভাবে কাজ করে তা বোঝার জন্য
3. **Performance Testing**: বড় team এর সাথে rank calculation test করার জন্য
4. **Bug Testing**: Rank upgrade logic এ কোন bug আছে কিনা চেক করার জন্য

## নোট

- এই seeder multiple বার চালানো যায়
- `firstOrCreate` ব্যবহার করা হয়েছে তাই duplicate data তৈরি হবে না
- Real production data এর সাথে conflict হবে না
- Test শেষে manually data delete করার প্রয়োজন নেই