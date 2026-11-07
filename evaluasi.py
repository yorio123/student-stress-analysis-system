import mysql.connector
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns

# ============================================================
# 1. DATABASE CONNECTION
# ============================================================

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="db_analisis"
)

# ============================================================
# 2. RETRIEVE FUZZY RESULTS
# ============================================================

query = """
SELECT
    d.data_id,
    d.nama,
    d.jurusan,
    d.semester,

    hf.score_paw,
    hf.score_tm,
    hf.score_pss,
    hf.score_fs,
    hf.score_sq,

    hf.norm_paw,
    hf.norm_tm,
    hf.norm_pss,
    hf.norm_fs,
    hf.norm_sq,

    hf.stress_score,
    hf.stress_category

FROM hasil_fuzzy hf
JOIN data d ON d.data_id = hf.data_id
"""

try:
    df = pd.read_sql(query, conn)
finally:
    conn.close()

if df.empty:
    raise ValueError(
        "No respondent data found in the hasil_fuzzy table."
    )

print("\n===== RESPONDENT DATA =====")
print(df.head())
print("\nTotal respondents:", len(df))


# ============================================================
# 3. STANDARDIZE STRESS CATEGORY LABELS
# ============================================================

# Supports Indonesian and English category names.
category_mapping = {
    "rendah": "Low",
    "low": "Low",
    "sedang": "Moderate",
    "moderate": "Moderate",
    "tinggi": "High",
    "high": "High"
}

df["stress_category"] = (
    df["stress_category"]
    .astype("string")
    .str.strip()
    .str.casefold()
    .map(category_mapping)
)

if df["stress_category"].isna().any():
    unknown = df.loc[
        df["stress_category"].isna(),
        "data_id"
    ].tolist()

    raise ValueError(
        "Some stress categories are missing or unrecognized. "
        f"Check data_id values: {unknown[:10]}"
    )

category_order = ["Low", "Moderate", "High"]

# Use consistent names throughout the analysis.
factor_mapping = {
    "Academic Workload": "norm_paw",
    "Time Management": "norm_tm",
    "Social Support": "norm_pss",
    "Financial Condition": "norm_fs",
    "Sleep Quality": "norm_sq"
}

factor_columns = list(factor_mapping.values())

# ============================================================
# 4. STRESS CATEGORY DISTRIBUTION
# ============================================================

category_counts = (
    df["stress_category"]
    .value_counts()
    .reindex(category_order, fill_value=0)
)

category_percentages = (
    category_counts / len(df) * 100
)

category_summary = pd.DataFrame({
    "Number of Students": category_counts,
    "Percentage (%)": category_percentages.round(2)
})

print("\n===== STRESS LEVEL DISTRIBUTION =====")
print(category_summary)

# ============================================================
# 5. STRESS CATEGORY DISTRIBUTION CHART
# ============================================================

plt.figure(figsize=(8, 5))

ax = category_counts.plot(
    kind="bar",
    rot=0
)

plt.title("Distribution of Stress Levels Among Final-Year Students")
plt.xlabel("Stress Category")
plt.ylabel("Number of Students")

for i, value in enumerate(category_counts):
    ax.text(
        i,
        value + max(len(df) * 0.01, 0.1),
        f"{value}\n({category_percentages.iloc[i]:.1f}%)",
        ha="center",
        va="bottom"
    )

plt.ylim(
    0,
    max(category_counts.max() * 1.18, 1)
)

plt.tight_layout()
plt.show()


# ============================================================
# 6. AVERAGE NORMALIZED SCORE OF EACH FACTOR
# ============================================================

average_factors = (
    df[factor_columns]
    .mean()
    .rename({
        "norm_paw": "Academic Workload",
        "norm_tm": "Time Management",
        "norm_pss": "Social Support",
        "norm_fs": "Financial Condition",
        "norm_sq": "Sleep Quality"
    })
    .sort_values(ascending=False)
)

print("\n===== AVERAGE NORMALIZED FACTOR SCORES =====")
print(average_factors.round(3))

# ============================================================
# 7. AVERAGE FACTOR SCORE CHART
# ============================================================

plt.figure(figsize=(10, 5))

ax = average_factors.plot(kind="bar", rot=0)

plt.title("Average Normalized Scores of Input Factors")
plt.xlabel("Input Factor")
plt.ylabel("Average Normalized Score (0–100)")

for i, value in enumerate(average_factors):
    ax.text(
        i,
        value + 1,
        f"{value:.2f}",
        ha="center",
        va="bottom"
    )

plt.ylim(
    0,
    max(average_factors.max() * 1.15, 1)
)

plt.tight_layout()
plt.show()


# ============================================================
# 8. AVERAGE FACTOR SCORES BY STRESS CATEGORY
# ============================================================

factor_names = list(factor_mapping.keys())

average_by_category = (
    df.groupby("stress_category")[factor_columns]
    .mean()
    .reindex(category_order)
)

average_by_category.columns = factor_names

print("\n===== AVERAGE FACTOR SCORES BY STRESS CATEGORY =====")
print(average_by_category.round(3))

# ============================================================
# 9. GROUPED BAR CHART: FACTORS BY STRESS CATEGORY
# ============================================================

ax = average_by_category.plot(
    kind="bar",
    figsize=(11, 6),
    rot=0
)

plt.title("Average Input Factor Scores by Stress Category")
plt.xlabel("Stress Category")
plt.ylabel("Average Normalized Score (0–100)")
plt.legend(title="Input Factor")

plt.tight_layout()
plt.show()


# ============================================================
# 10. CORRELATION BETWEEN INPUT FACTORS AND STRESS SCORE
# ============================================================

correlation_data = df[
    factor_columns + ["stress_score"]
].copy()

correlation_data.columns = factor_names + ["Stress Score"]

correlation = (
    correlation_data
    .corr(numeric_only=True)["Stress Score"]
    .drop("Stress Score")
)

correlation = correlation.sort_values(
    key=lambda values: values.abs(),
    ascending=False
)

print("\n===== CORRELATION WITH STRESS SCORE =====")
print(correlation.round(4))

# ============================================================
# 11. CORRELATION BAR CHART
# ============================================================

plt.figure(figsize=(10, 5))

ax = correlation.plot(kind="bar", rot=0)

plt.title("Correlation Between Input Factors and Stress Scores")
plt.xlabel("Input Factor")
plt.ylabel("Pearson Correlation Coefficient")
plt.axhline(y=0, linewidth=1)

for i, value in enumerate(correlation):
    offset = 0.025 if value >= 0 else -0.04

    ax.text(
        i,
        value + offset,
        f"{value:.3f}",
        ha="center",
        va="bottom" if value >= 0 else "top"
    )

plt.tight_layout()
plt.show()


# ============================================================
# 12. SCATTER PLOTS WITH REGRESSION LINES
# ============================================================

sns.set_theme(style="whitegrid")

for factor in factor_names:

    plt.figure(figsize=(8, 5))

    ax = sns.regplot(
        data=correlation_data,
        x=factor,
        y="Stress Score",
        scatter_kws={"alpha": 0.6},
        line_kws={}
    )

    r_value = correlation[factor]

    plt.title(
        f"Relationship Between {factor} and Stress Score"
    )
    plt.xlabel(f"{factor} (Normalized Score)")
    plt.ylabel("Stress Score")

    plt.text(
        0.05,
        0.95,
        f"Pearson r = {r_value:.3f}",
        transform=ax.transAxes,
        verticalalignment="top"
    )

    plt.tight_layout()
    plt.show()