import numpy as np

np.random.seed(42)
random_values = [np.random.randn() for _ in range(1000)]
for value in random_values:
    print(value)
