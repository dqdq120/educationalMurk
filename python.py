from wordfreq import top_n_list
import json

# Get top 5000 English words
top5000 = top_n_list("en", 40000)

# Save to JSON
with open("top40000_words.json", "w", encoding="utf8") as f:
    json.dump(top5000, f, ensure_ascii=False, indent=2)

print("Saved to top40000_words.json")
