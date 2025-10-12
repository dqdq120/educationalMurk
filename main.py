import os
import json
from collections import defaultdict
from tqdm import tqdm  # <- for the progress bar

# Folder containing your JSON files
folder_path = "words"

# Output folder for merged files
output_folder = "merged_words"
os.makedirs(output_folder, exist_ok=True)

# Dictionary to hold grouped data
merged_data = defaultdict(list)

# Get all JSON files in the folder
json_files = [f for f in os.listdir(folder_path) if f.endswith(".json")]

print(f"📁 Found {len(json_files)} JSON files. Starting merge...\n")

# Iterate with a progress bar
for filename in tqdm(json_files, desc="Merging JSON files", unit="file"):
    file_path = os.path.join(folder_path, filename)
    try:
        with open(file_path, "r", encoding="utf-8") as f:
            data = json.load(f)

            # Handle list of words or word objects
            if isinstance(data, list):
                for item in data:
                    if isinstance(item, str):
                        first_letter = item[0].lower()
                        merged_data[first_letter].append(item)
                    elif isinstance(item, dict) and "word" in item:
                        first_letter = item["word"][0].lower()
                        merged_data[first_letter].append(item)

            # Handle single object
            elif isinstance(data, dict):
                if "word" in data:
                    first_letter = data["word"][0].lower()
                    merged_data[first_letter].append(data)

            # Handle single string
            elif isinstance(data, str):
                first_letter = data[0].lower()
                merged_data[first_letter].append(data)

    except Exception as e:
        tqdm.write(f"⚠️ Error reading {filename}: {e}")

# Save merged results with another progress bar
print("\n💾 Saving merged files...")
for letter in tqdm(sorted(merged_data.keys()), desc="Writing output", unit="letter"):
    output_path = os.path.join(output_folder, f"{letter}.json")
    with open(output_path, "w", encoding="utf-8") as f:
        json.dump(merged_data[letter], f, ensure_ascii=False, indent=2)

print("\n✅ All JSON files have been merged successfully!")
