import os
import random
from datetime import datetime, timedelta, date

# --- SETTINGS ---
# Oct 1, 2025 to Dec 31, 2025
start_date = date(2025, 10, 1)
end_date = date(2025, 12, 31)

# File to update
file_name = "contribution_log.txt"

defdaterange = end_date - start_date
for i in range(defdaterange.days + 1):
    current_date = start_date + timedelta(days=i)
    
    # 1. Weekends (Saturday=5, Sunday=6) - 90% chance NO WORK
    if current_date.weekday() >= 5:
        if random.random() < 0.9: # 90% chance to skip weekend
            print(f"Skipping weekend: {current_date}")
            continue

    # 2. Weekdays - 20% chance LEAVE (Random leave)
    if random.random() < 0.2: 
        print(f"Taking a random leave on: {current_date}")
        continue

    # 3. Random Number of Commits (1 to 6 times per day)
    commits_per_day = random.randint(1, 6)
    
    for j in range(commits_per_day):
        # Time set (Morning 9 AM to Night 11 PM)
        hour = random.randint(9, 23)
        minute = random.randint(0, 59)
        
        # Date Format for Git
        date_str = f"{current_date} {hour}:{minute}:00"
        
        # Write to file
        with open(file_name, "a") as f:
            f.write(f"Work update on {date_str}\n")
        
        # Git Command to Commit on Past Date
        os.system("git add .")
        os.system(f'git commit --date="{date_str}" -m "Update logic for backend fix"')

print("SUCCESS: Contributions generated for Oct-Dec 2025!")