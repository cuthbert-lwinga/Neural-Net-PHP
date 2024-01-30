#include <iostream>
#include <chrono>
#include <vector>
#include <thread>
#include <stdexcept>
#include "Utility.h"  
#include "rapidjson/document.h"

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

    // Process JSON data
    rapidjson::Document doc;
    doc.Parse(jsonData.c_str());
    if (doc.HasParseError()) {
        std::cerr << "Error: Invalid JSON data" << std::endl;
        return 1;
    }

    // Check if the data for key 'a' and 'b' are 1D or 2D
    // std::vector<std::vector<double>> jacobianResult;
    // if (doc["a"].IsArray() && doc["a"][0].IsArray()) {
    //     // 2D case
    //     std::vector<std::vector<double>> output = extractMatrixData(jsonData, "a");
    //     std::vector<std::vector<double>> dvalues = extractMatrixData(jsonData, "b");
    //     jacobianResult = jacobianMatrix2D(output, dvalues);
    // } else {
    //     // 1D case
    //     std::vector<double> output = extractVectorData(jsonData, "a");
    //     std::vector<double> dvalues = extractVectorData(jsonData, "b");
    //     jacobianResult = jacobianMatrix1D(output, dvalues);
    // }

    // // Save the result to JSON
    // if (saveToJSON(outputFilePath, jacobianResult)) {
        std::cout << "Successful" << std::endl;
    // } else {
    //     std::cout << "Fail" << std::endl;
    // }

    // auto end_time = std::chrono::high_resolution_clock::now();
    // auto duration = std::chrono::duration_cast<std::chrono::milliseconds>(end_time - start_time);
    // std::cout << std::endl << "Elapsed time: " << duration.count() << " milliseconds" << std::endl;

    return 0;
}
