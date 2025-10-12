from wordfreq import top_n_list
import json

# Get top 5000 English words
top5000 = top_n_list("en", 5000)

# Save to JSON
with open("top5000_words.json", "w", encoding="utf8") as f:
    json.dump(top5000, f, ensure_ascii=False, indent=2)

print("Saved to top5000_words.json")
