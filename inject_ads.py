import os

# Script iklan yang akan disisipkan
ad_script = """
<script>
  atOptions = {
    'key' : 'f9e40eb3b3cd05f1038aac7b36b33ad2',
    'format' : 'iframe',
    'height' : 90,
    'width' : 728,
    'params' : {}
  };
</script>
<script src="https://bicea.org/22/f9e40eb3b3cd05f1038aac7b36b33ad2"></script>
"""

# Tentukan folder tempat file HTML berada (sesuaikan jika ada di dalam folder tertentu)
target_dir = "."  # Titik berarti folder saat ini

count = 0
for root, dirs, files in os.walk(target_dir):
    for file in files:
        if file.endswith(".html"):
            file_path = os.path.join(root, file)
            
            with open(file_path, "r", encoding="utf-8", errors="ignore") as f:
                content = f.read()
            
            # Cek agar iklan tidak ter-inject dua kali di file yang sama
            if "f9e40eb3b3cd05f1038aac7b36b33ad2" in content:
                print(f"Lewati (sudah ada iklan): {file_path}")
                continue
            
            # Sisipkan iklan sebelum tag penutup </body>
            if "</body>" in content:
                new_content = content.replace("</body>", f"\n{ad_script}\n</body>")
            else:
                # Jika tidak ada tag </body>, tempel di paling bawah file
                new_content = content + f"\n{ad_script}\n"
            
            with open(file_path, "w", encoding="utf-8") as f:
                f.write(new_content)
            
            count += 1
            print(f"Berhasil injek iklan ke: {file_path}")

print(f"\nSelesai! Total {count} file HTML berhasil disisipi iklan.")
