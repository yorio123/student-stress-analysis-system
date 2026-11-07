import mysql.connector
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
from sklearn.metrics import (
    confusion_matrix,
    accuracy_score,
    precision_score,
    recall_score,
    f1_score,
    mean_absolute_error,
    mean_squared_error
)
from openpyxl import load_workbook
from openpyxl.styles import Font, Alignment
from openpyxl.utils import get_column_letter


# ==========================================================
# 1. KONFIGURASI DATABASE
# ==========================================================

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "db_analisis"
}


# ==========================================================
# 2. NAMA FILE EXCEL
# ==========================================================

OUTPUT_FILE = "evaluasi_fuzzy_mamdani.xlsx"


# ==========================================================
# 3. KATEGORI
# ==========================================================

CATEGORIES = [
    "Rendah",
    "Sedang",
    "Tinggi"
]


# ==========================================================
# 4. KONEKSI DATABASE
# ==========================================================

print("=" * 60)
print("EVALUASI FUZZY MAMDANI")
print("=" * 60)

print("[1] Menghubungkan ke database...")

try:

    conn = mysql.connector.connect(**DB_CONFIG)

    print("    Database berhasil terhubung.")

except mysql.connector.Error as err:

    print("ERROR DATABASE:")
    print(err)
    exit()


# ==========================================================
# 5. AMBIL DATA MANUAL DAN FUZZY
# ==========================================================

print("[2] Mengambil data hasil manual dan fuzzy...")


query = """
SELECT
    d.nama,

    hp.score_paw,
    hp.score_tm,
    hp.score_pss,
    hp.score_fs,
    hp.score_sq,

    hp.stress_score AS manual_score,
    hp.stress_category AS manual_category,

    hf.stress_score AS fuzzy_score,
    hf.stress_category AS fuzzy_category

FROM hasil_perhitungan hp

INNER JOIN hasil_fuzzy hf
    ON hp.data_id = hf.data_id

INNER JOIN data d
    ON hp.data_id = d.data_id

ORDER BY hp.data_id
"""


try:

    df = pd.read_sql(query, conn)

except Exception as err:

    print("ERROR QUERY:")
    print(err)

    conn.close()
    exit()


conn.close()


print(f"    Jumlah data: {len(df)}")


if len(df) == 0:

    print("Tidak terdapat data evaluasi.")
    exit()


# ==========================================================
# 6. NORMALISASI KATEGORI
# ==========================================================

df["manual_category"] = (
    df["manual_category"]
    .astype(str)
    .str.strip()
    .str.capitalize()
)

df["fuzzy_category"] = (
    df["fuzzy_category"]
    .astype(str)
    .str.strip()
    .str.capitalize()
)


# ==========================================================
# 7. HITUNG SELISIH SCORE
# ==========================================================

# Selisih signed
# Fuzzy - Manual

df["selisih_score"] = (
    df["fuzzy_score"] -
    df["manual_score"]
)


# Selisih absolut

df["absolute_error"] = (
    df["fuzzy_score"] -
    df["manual_score"]
).abs()


# Status kategori

df["status_kategori"] = np.where(
    df["manual_category"] == df["fuzzy_category"],
    "Sesuai",
    "Tidak Sesuai"
)


# ==========================================================
# 8. DATA UNTUK CONFUSION MATRIX
# ==========================================================

y_true = df["manual_category"]

y_pred = df["fuzzy_category"]


# ==========================================================
# 9. CONFUSION MATRIX
# ==========================================================

cm = confusion_matrix(
    y_true,
    y_pred,
    labels=CATEGORIES
)


cm_df = pd.DataFrame(
    cm,
    index=CATEGORIES,
    columns=CATEGORIES
)

cm_df.index.name = "Manual \\ Fuzzy"


# ==========================================================
# 10. ACCURACY
# ==========================================================

accuracy = accuracy_score(
    y_true,
    y_pred
)


# ==========================================================
# 11. PRECISION
# ==========================================================

precision = precision_score(
    y_true,
    y_pred,
    labels=CATEGORIES,
    average=None,
    zero_division=0
)


# ==========================================================
# 12. RECALL
# ==========================================================

recall = recall_score(
    y_true,
    y_pred,
    labels=CATEGORIES,
    average=None,
    zero_division=0
)


# ==========================================================
# 13. F1-SCORE
# ==========================================================

f1 = f1_score(
    y_true,
    y_pred,
    labels=CATEGORIES,
    average=None,
    zero_division=0
)


# ==========================================================
# 14. MACRO AVERAGE
# ==========================================================

precision_macro = precision_score(
    y_true,
    y_pred,
    labels=CATEGORIES,
    average="macro",
    zero_division=0
)

recall_macro = recall_score(
    y_true,
    y_pred,
    labels=CATEGORIES,
    average="macro",
    zero_division=0
)

f1_macro = f1_score(
    y_true,
    y_pred,
    labels=CATEGORIES,
    average="macro",
    zero_division=0
)


# ==========================================================
# 15. MAE
# ==========================================================

mae = mean_absolute_error(
    df["manual_score"],
    df["fuzzy_score"]
)


# ==========================================================
# 16. RMSE
# ==========================================================

rmse = np.sqrt(
    mean_squared_error(
        df["manual_score"],
        df["fuzzy_score"]
    )
)


# ==========================================================
# 17. TABEL METRIK
# ==========================================================

metrics_df = pd.DataFrame({

    "Kategori": CATEGORIES,

    "Precision": precision,

    "Recall": recall,

    "F1-Score": f1

})


macro_row = pd.DataFrame({

    "Kategori": ["Macro Average"],

    "Precision": [precision_macro],

    "Recall": [recall_macro],

    "F1-Score": [f1_macro]

})


metrics_df = pd.concat(
    [metrics_df, macro_row],
    ignore_index=True
)


# ==========================================================
# 18. SUMMARY
# ==========================================================

summary_df = pd.DataFrame({

    "Metrik": [
        "Jumlah Data",
        "Data Sesuai Kategori",
        "Data Tidak Sesuai Kategori",
        "Accuracy",
        "Precision Macro",
        "Recall Macro",
        "F1-Score Macro",
        "MAE",
        "RMSE"
    ],

    "Nilai": [
        len(df),

        (df["status_kategori"] == "Sesuai").sum(),

        (df["status_kategori"] == "Tidak Sesuai").sum(),

        accuracy,

        precision_macro,

        recall_macro,

        f1_macro,

        mae,

        rmse
    ]

})


# ==========================================================
# 19. DATA DETAIL
# ==========================================================

detail_columns = [

    "nama",

    "score_paw",
    "score_tm",
    "score_pss",
    "score_fs",
    "score_sq",

    "manual_score",
    "fuzzy_score",

    "selisih_score",
    "absolute_error",

    "manual_category",
    "fuzzy_category",

    "status_kategori"
]


detail_df = df[detail_columns].copy()


# ==========================================================
# 20. SIMPAN KE EXCEL
# ==========================================================

print("[3] Menyimpan hasil evaluasi ke Excel...")


with pd.ExcelWriter(
    OUTPUT_FILE,
    engine="openpyxl"
) as writer:

    detail_df.to_excel(
        writer,
        sheet_name="Detail Data",
        index=False
    )

    cm_df.to_excel(
        writer,
        sheet_name="Confusion Matrix"
    )

    metrics_df.to_excel(
        writer,
        sheet_name="Metrics",
        index=False
    )

    summary_df.to_excel(
        writer,
        sheet_name="Summary",
        index=False
    )


# ==========================================================
# 21. FORMAT EXCEL
# ==========================================================

print("[4] Memformat file Excel...")


wb = load_workbook(
    OUTPUT_FILE
)


for ws in wb.worksheets:

    # Header
    for cell in ws[1]:

        cell.font = Font(
            bold=True
        )

        cell.alignment = Alignment(
            horizontal="center",
            vertical="center"
        )


    # Semua isi
    for row in ws.iter_rows():

        for cell in row:

            cell.alignment = Alignment(
                vertical="center"
            )


    # Lebar kolom otomatis
    for column_cells in ws.columns:

        max_length = 0

        column_letter = get_column_letter(
            column_cells[0].column
        )

        for cell in column_cells:

            try:

                length = len(
                    str(cell.value)
                )

                if length > max_length:
                    max_length = length

            except:

                pass


        ws.column_dimensions[
            column_letter
        ].width = min(
            max_length + 2,
            30
        )


# ==========================================================
# 22. FORMAT ANGKA
# ==========================================================

ws = wb["Detail Data"]


# Cari kolom berdasarkan nama
headers = {
    cell.value: cell.column
    for cell in ws[1]
}


for row in range(2, ws.max_row + 1):

    # Manual score
    ws.cell(
        row,
        headers["manual_score"]
    ).number_format = "0.00"


    # Fuzzy score
    ws.cell(
        row,
        headers["fuzzy_score"]
    ).number_format = "0.00"


    # Selisih
    ws.cell(
        row,
        headers["selisih_score"]
    ).number_format = "0.00"


    # Absolute Error
    ws.cell(
        row,
        headers["absolute_error"]
    ).number_format = "0.00"


# ==========================================================
# 23. FORMAT METRICS
# ==========================================================

ws = wb["Metrics"]


for row in range(2, ws.max_row + 1):

    for col in range(2, 5):

        ws.cell(
            row,
            col
        ).number_format = "0.00%"


# ==========================================================
# 24. FORMAT SUMMARY
# ==========================================================

ws = wb["Summary"]


for row in range(2, ws.max_row + 1):

    metric = ws.cell(
        row,
        1
    ).value


    if metric in [
        "Accuracy",
        "Precision Macro",
        "Recall Macro",
        "F1-Score Macro"
    ]:

        ws.cell(
            row,
            2
        ).number_format = "0.00%"

    elif metric in [
        "MAE",
        "RMSE"
    ]:

        ws.cell(
            row,
            2
        ).number_format = "0.00"


# ==========================================================
# 25. SIMPAN
# ==========================================================

wb.save(
    OUTPUT_FILE
)
# ==========================================================
# 9A. TAMPILKAN CONFUSION MATRIX
# ==========================================================

plt.figure(figsize=(7, 6))

plt.imshow(cm, interpolation="nearest")
plt.title("Confusion Matrix - Fuzzy Mamdani")
plt.colorbar()

plt.xticks(
    np.arange(len(CATEGORIES)),
    CATEGORIES
)

plt.yticks(
    np.arange(len(CATEGORIES)),
    CATEGORIES
)

plt.xlabel("Prediksi Fuzzy Mamdani")
plt.ylabel("Kategori Manual")

threshold = cm.max() / 2 if cm.size else 0

for i in range(cm.shape[0]):
    for j in range(cm.shape[1]):
        plt.text(
            j,
            i,
            str(cm[i, j]),
            ha="center",
            va="center",
            color="white" if cm[i, j] > threshold else "black",
            fontsize=12,
            fontweight="bold"
        )

plt.tight_layout()
plt.show()

# ==========================================================
# 26. TAMPILKAN HASIL DI TERMINAL
# ==========================================================

print()
print("=" * 60)
print("HASIL EVALUASI")
print("=" * 60)

print(f"Jumlah data       : {len(df)}")
print(f"Data sesuai       : {(df['status_kategori'] == 'Sesuai').sum()}")
print(f"Data tidak sesuai : {(df['status_kategori'] == 'Tidak Sesuai').sum()}")

print()

print(f"Accuracy          : {accuracy * 100:.2f}%")
print(f"Precision Macro   : {precision_macro * 100:.2f}%")
print(f"Recall Macro      : {recall_macro * 100:.2f}%")
print(f"F1-Score Macro    : {f1_macro * 100:.2f}%")

print()

print(f"MAE               : {mae:.2f}")
print(f"RMSE              : {rmse:.2f}")

print()
print("=" * 60)
print(f"File Excel berhasil dibuat:")
print(OUTPUT_FILE)
print("=" * 60)