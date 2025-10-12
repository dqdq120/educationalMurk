# import json
# from tqdm import tqdm   # pip install tqdm

# # ✅ Common prefixes for verbs/nouns
# prefixes = [
#     "un","re","dis","mis","pre","over","under","out","up","down",
#     "in","im","il","ir","non","inter","sub","super","trans","co",
#     "con","de","en","em","pro","semi","anti","auto"
# ]

# # ✅ Common suffixes for verbs/nouns
# suffixes = [
#     "s","es","ed","ing","er","or","ist","ian","ment","tion","sion",
#     "ness","ity","al","ance","ence","ship","hood","dom","ful","less",
#     "ous","ive","ize","ise","ate","en","ify","ly"
# ]

# # Load the grouped words JSON
# with open("english_words_grouped.json", "r", encoding="utf-8") as f:
#     data = json.load(f)

# # ✅ Flatten words into a set for O(1) lookups
# all_words = {word for words in data.values() for word in words}

# # Dictionary: root → {"prefixes": set(), "suffixes": set()}
# root_affixes = {}

# for word in tqdm(all_words, total=len(all_words), desc="Processing words"):
#     # --- Check prefixes ---
#     for prefix in prefixes:
#         if word.startswith(prefix) and len(word) > len(prefix) + 2:
#             root = word[len(prefix):]
#             if root in all_words:  # O(1) check
#                 entry = root_affixes.setdefault(root, {"prefixes": set(), "suffixes": set()})
#                 entry["prefixes"].add(prefix)

#     # --- Check suffixes ---
#     for suffix in suffixes:
#         if word.endswith(suffix) and len(word) > len(suffix) + 2:
#             root = word[:-len(suffix)]
#             if root in all_words:  # O(1) check
#                 entry = root_affixes.setdefault(root, {"prefixes": set(), "suffixes": set()})
#                 entry["suffixes"].add(suffix)

# # Convert sets → lists for JSON
# root_affixes = {root: {"prefixes": list(v["prefixes"]), "suffixes": list(v["suffixes"])}
#                 for root, v in root_affixes.items()}

# # Save results
# with open("english_roots_affixes.json", "w", encoding="utf-8") as f:
#     json.dump(root_affixes, f, ensure_ascii=False, indent=2)

# print("✅ Dictionary saved to english_roots_affixes.json")



























import json
import os
import re
from tqdm import tqdm
from nltk.corpus import wordnet as wn
import nltk

# First time only: download WordNet if missing
try:
    wn.synsets("test")
except LookupError:
    nltk.download("wordnet")
    nltk.download("omw-1.4")

# Load words
with open("english_words.json", "r", encoding="utf-8") as f:
    words = json.load(f)

# Load prefixes/suffixes data
with open("english_roots_affixes.json", "r", encoding="utf-8") as f:
    root_affixes_data = json.load(f)

# Output folder
out_dir = "words"
os.makedirs(out_dir, exist_ok=True)

def safe_filename(name: str) -> str:
    return re.sub(r'[^a-zA-Z0-9_-]', '_', name)

def fetch_definition(word):
    synsets = wn.synsets(word)
    return synsets[0].definition() if synsets else "No definition available."

def fetch_examples(word):
    synsets = wn.synsets(word)
    examples = []
    for s in synsets:
        examples.extend(s.examples())
    return examples[:5] if examples else []

# Build JSON files
for word in tqdm(words, total=len(words), desc="Saving dictionary files"):
    entry = {
        "word": word,
        "definition": fetch_definition(word),
        "prefixes": root_affixes_data.get(word, {}).get("prefixes", []),
        "suffixes": root_affixes_data.get(word, {}).get("suffixes", []),
        "examples": fetch_examples(word),
    }

    filename = safe_filename(word) + ".json"
    filepath = os.path.join(out_dir, filename)
    with open(filepath, "w", encoding="utf-8") as f:
        json.dump(entry, f, ensure_ascii=False, indent=2)

print(f"✅ Dictionary JSON created for {len(words)} words in '{out_dir}/'")
