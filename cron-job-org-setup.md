# Cron-job.org Setup Guide

## 🌐 **Single URL Cron Job Configuration**

### **Your Domain URL:**
Replace `yourdomain.com` with your actual domain name.

---

## 📋 **Single Cron Job Setup on cron-job.org**

### **All-in-One Cron Job (Every 5 minutes)**
```
URL: https://yourdomain.com/cron
Frequency: Every 5 minutes
Description: Process all cron jobs including:
- BEP20 USDT Deposits
- Order Processing
- Auto-receive Processing
- Withdrawal Processing
- Investment Profits (weekdays at 9 AM)
- BEP20 Monitoring
```

---

## 🔧 **Setup Steps on cron-job.org**

### **Step 1: Register/Login**
1. Go to [cron-job.org](https://cron-job.org)
2. Register or login to your account

### **Step 2: Create Single Cron Job**
1. Click "Create Cronjob"
2. Fill in the details:
   - **URL**: `https://yourdomain.com/cron`
   - **Frequency**: Every 5 minutes
   - **Description**: All-in-one cron job
3. Save the cron job

### **Step 3: Test URL**
Test the URL in your browser to make sure it works:
- `https://yourdomain.com/cron`

---

## 📊 **Expected Response Format**

The cron URL will return the standard response from CronController:
- Success: Normal page response
- Error: Error page (check logs for details)

---

## ⚠️ **Important Notes**

1. **Replace Domain**: Update `yourdomain.com` with your actual domain
2. **HTTPS Required**: Make sure your site has SSL certificate
3. **No Authentication**: This URL doesn't require login
4. **Rate Limiting**: cron-job.org has rate limits, adjust frequency if needed
5. **Monitoring**: Check cron-job.org dashboard for execution logs
6. **All-in-One**: Single URL handles all cron jobs

---

## 🎯 **What Gets Processed**

### **Every 5 Minutes:**
- ✅ BEP20 USDT Deposits
- ✅ Order Processing
- ✅ Auto-receive Processing
- ✅ Withdrawal Processing
- ✅ BEP20 Monitoring

### **Daily at 9:00 AM (Weekdays Only):**
- ✅ Investment Profits Distribution

---

## 🔍 **Testing Commands**

### **Test Single URL:**
```bash
# Test all cron jobs
curl https://yourdomain.com/cron
```

### **Check Logs:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check cron execution
grep "Cron job" storage/logs/laravel.log
```
