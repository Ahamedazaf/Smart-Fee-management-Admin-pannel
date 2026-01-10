import os
import random

# --- SETTINGS ---
# Date: January 10, 2026
target_date = "2026-01-10"
file_name = "contribution_log.txt"

# Create 4 to 7 commits for this single day to look like "Normal Work"
commits_count = random.randint(4, 7)

print(f"Generating {commits_count} commits for {target_date}...")

for i in range(commits_count):
    # Random time between 10 AM and 9 PM
    hour = random.randint(10, 21)
    minute = random.randint(0, 59)
    
    # Git Date Format
    date_str = f"{target_date} {hour}:{minute}:00"
    
    # Update file
    with open(file_name, "a") as f:
        f.write(f"Work update on {date_str}\n")
    
    # Git Commands
    os.system("git add .")
    os.system(f'git commit --date="{date_str}" -m "Fixes and updates for Jan 10"')

print("SUCCESS! Jan 10 contributions ready.")