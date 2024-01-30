// Main.cpp
#include <iostream>
#include <chrono>
#include "Utility.h"
#include "Matrix.h"

int main(int argc, char* argv[]) {
    if (argc != 3) {
        std::cerr << "Usage: " << argv[0] << " <file-path> <output-file-path>" << std::endl;
        return 1;
    }

    auto start_time = std::chrono::high_resolution_clock::now();
    std::string filePath(argv[1]);
    std::string outputFilePath(argv[2]);
    std::string jsonData = readJsonFromFile(filePath);

    if (jsonData.empty()) {
        std::cerr << "Failed to read JSON data from the file." << std::endl;
        return 1;
    }

    std::vector<std::vector<float>> matrixA = extractMatrixData(jsonData, "a");
    std::vector<std::vector<float>> matrixB = extractMatrixData(jsonData, "b");

    Matrix<float> a(1, 1);
    Matrix<float> b(1, 1);
    a.initialize(matrixA);
    b.initialize(matrixB);

    // Change here: Use the add method instead of dot
    Matrix<float> output = a.add(b);

    if (saveToJSON(outputFilePath, output.getData())) {
        std::cout << "Successful" << std::endl;
    } else {
        std::cout << "Fail" << std::endl;
    }

    auto end_time = std::chrono::high_resolution_clock::now();
    auto duration = std::chrono::duration_cast<std::chrono::milliseconds>(end_time - start_time);

    // Optionally, you can uncomment this to display elapsed time
    std::cout << std::endl << "Elapsed time: " << duration.count() << " milliseconds" << std::endl;

    return 0;
}
