#include "Matrix.h"
#include <thread>
#include <vector>

template<typename T>
std::mutex Matrix<T>::index_mutex;

template<typename T>
int Matrix<T>::next_row = 0;

template<typename T>
Matrix<T>::Matrix(int n, int m) : rows(n), cols(m), data(n, std::vector<T>(m)) {}

template<typename T>
int Matrix<T>::getRows() const {
    return rows;
}

template<typename T>
int Matrix<T>::getCols() const {
    return cols;
}

template<typename T>
std::vector<std::vector<T>> Matrix<T>::getData() const {
    return data;
}


template<typename T>
void Matrix<T>::initialize(const std::vector<std::vector<T>>& value) {
    rows = value.size();
    cols = (rows > 0) ? value[0].size() : 0;
    data = value;
}

template<typename T>
void Matrix<T>::print() const {
    for (int i = 0; i < rows; ++i) {
        std::cout << '[';
        for (int j = 0; j < cols; ++j) {
            std::cout << data[i][j] << (j == (cols - 1) ? ' ' : ',');
        }
        std::cout << ']' << std::endl;
    }
}

template<typename T>
void* Matrix<T>::threadFunction(void* arg) {
    ThreadData* data = static_cast<ThreadData*>(arg);
    for (int i = data->start_row; i < data->end_row; ++i) {
        for (int j = 0; j < data->other->cols; ++j) {
            for (int k = 0; k < data->self->cols; ++k) {
                data->result->data[i][j] += data->self->data[i][k] * data->other->data[k][j];
            }
        }
    }
    return nullptr;
}



template<typename T>
Matrix<T> Matrix<T>::add(const Matrix<T>& other) const {
    if (this->rows != other.getRows() || this->cols != other.getCols()) {
        throw std::invalid_argument("Matrices dimensions must be equal for addition");
    }

    Matrix<T> result(this->rows, this->cols);
    const size_t SmallMatrixThreshold = 10000;  // Threshold for small matrices
    size_t totalElements = this->rows * this->cols;

    if (totalElements < SmallMatrixThreshold) {
        // Single-threaded addition
        for (size_t i = 0; i < this->rows; ++i) {
            for (size_t j = 0; j < this->cols; ++j) {
                result.data[i][j] = this->data[i][j] + other.data[i][j];
            }
        }
    } else {
        // Multi-threaded addition
        size_t hardwareThreads = 2;//std::thread::hardware_concurrency();


        std::cout << std::endl << "threads: " << hardwareThreads << " count" << std::endl;
        size_t rowsPerThread = this->rows / hardwareThreads;
        std::vector<std::thread> threads;

        for (size_t t = 0; t < hardwareThreads; ++t) {
            size_t startRow = t * rowsPerThread;
            size_t endRow = (t == hardwareThreads - 1) ? this->rows : startRow + rowsPerThread;

            threads.emplace_back([&, startRow, endRow]() {
                for (size_t i = startRow; i < endRow; ++i) {
                    for (size_t j = 0; j < this->cols; ++j) {
                        result.data[i][j] = this->data[i][j] + other.data[i][j];
                    }
                }
            });
        }

        for (auto& t : threads) {
            t.join();
        }
    }

    return result;
}



template<typename T>
Matrix<T> Matrix<T>::dot(const Matrix<T>& other) const {
    if (this->cols != other.getRows()) {
        throw std::invalid_argument("Matrix dimensions do not allow multiplication");
    }

    Matrix<T> result(this->rows, other.getCols());
    int totalWorkload = this->rows * other.getCols();

    int hardwareThreads = std::thread::hardware_concurrency() / 2;
    int workloadPerThread = std::max(1, totalWorkload / hardwareThreads);
    int threadCount = std::min(hardwareThreads, (totalWorkload + workloadPerThread - 1) / workloadPerThread);

    std::vector<std::thread> threads(threadCount);
    std::atomic<int> next_workload(0);

    for (int t = 0; t < threadCount; ++t) {
        threads[t] = std::thread([&]() {
            while (true) {
                int start_index = next_workload.fetch_add(workloadPerThread);
                if (start_index >= totalWorkload) break;

                int end_index = std::min(start_index + workloadPerThread, totalWorkload);

                for (int index = start_index; index < end_index; ++index) {
                    int row = index / other.getCols();
                    int col = index % other.getCols();

                    T sum = 0;
                    for (int k = 0; k < this->cols; ++k) {
                        sum += this->data[row][k] * other.data[k][col];
                    }

                    result.data[row][col] = sum;
                }
            }
        });
    }

    for (auto& t : threads) {
        t.join();
    }

    return result;
}

template<typename T>
Matrix<T> Matrix<T>::operator*(const Matrix<T>& other) const {
    return dot(other);
}

// Explicit instantiation for int and float
template class Matrix<int>;
template class Matrix<float>;
template class Matrix<double>;