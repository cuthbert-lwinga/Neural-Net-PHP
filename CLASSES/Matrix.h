#ifndef MATRIX_H
#define MATRIX_H

#include <vector>
#include <iostream>
#include <mutex>
#include <pthread.h>

template<typename T>
class Matrix {
    static const int ROWS_PER_THREAD = 100;
    static std::mutex index_mutex;
    static int next_row;

    struct ThreadData {
        const Matrix<T>* self;
        const Matrix<T>* other;
        Matrix<T>* result;
        int start_row;
        int end_row;
    };

    static void* threadFunction(void* arg);

public:
    // Constructors
    Matrix(int n, int m);

    // Accessor functions
    int getRows() const;
    int getCols() const;
    std::vector<std::vector<T>> getData() const;

    // Matrix operations
    void initialize(const std::vector<std::vector<T>>& value);
    void print() const;
    Matrix<T> dot(const Matrix<T>& other) const;
    Matrix<T> add(const Matrix<T>& other) const;

    // Overloaded operators
    Matrix<T> operator*(const Matrix<T>& other) const;

private:
    int rows;
    int cols;
    std::vector<std::vector<T>> data;
};

#endif // MATRIX_H
