#include "Utility.h"
#include "rapidjson/document.h"
#include "rapidjson/writer.h"
#include "rapidjson/stringbuffer.h"
#include <fstream>
#include <iostream>
#include <sstream>
#include <string>
#include <vector>
#include <limits>
#include <vector>
#include <thread>
#include <stdexcept>

// Function to check if a file exists
bool fileExists(const std::string& filename) {
    std::ifstream file(filename);
    return file.good();
}

// Function to generate a new filename
std::string generateFilename(int counter) {
    std::stringstream ss;
    ss << "matrix_" << counter << ".json";
    return ss.str();
}

// Function to save data to JSON and return filename
template<typename T>
bool saveToJSON(const std::string& filename, const std::vector<std::vector<T>>& data) {
    rapidjson::Document doc;
    doc.SetArray();
    rapidjson::Document::AllocatorType& allocator = doc.GetAllocator();

    for (const auto& row : data) {
        rapidjson::Value jsonRow(rapidjson::kArrayType);
        for (T value : row) {
            jsonRow.PushBack(rapidjson::Value().SetDouble(static_cast<double>(value)), allocator);
        }
        doc.PushBack(jsonRow, allocator);
    }

    rapidjson::StringBuffer buffer;
    rapidjson::Writer<rapidjson::StringBuffer> writer(buffer);
    doc.Accept(writer);

    // Write to file
    std::ofstream file(filename);
    if (file.is_open()) {
        file << buffer.GetString();
        file.close();
    } else {
        std::cerr << "Error opening file for writing." << std::endl;
        return false;
    }

    return true; // Return true if everything was successful
}

std::string readJsonFromFile(const std::string& filePath) {
    std::ifstream file(filePath);
    if (!file.is_open()) {
        std::cerr << "Failed to open file: " << filePath << std::endl;
        return "";
    }

    std::string jsonData((std::istreambuf_iterator<char>(file)), std::istreambuf_iterator<char>());
    file.close();
    return jsonData;
}

std::vector<std::vector<float>> extractMatrixData(const std::string& jsonData, const char* key) {
    std::vector<std::vector<float>> matrix;
    rapidjson::Document doc;
    if (doc.Parse(jsonData.c_str()).HasParseError()) {
        std::cerr << "Error parsing JSON data" << std::endl;
        return matrix;
    }

    if (!doc.HasMember(key) || !doc[key].IsArray()) {
        std::cerr << "Invalid or missing matrix data for key '" << key << "' in the JSON." << std::endl;
        return matrix;
    }

    const rapidjson::Value& jsonMatrix = doc[key];
    for (auto& jsonRow : jsonMatrix.GetArray()) {
        std::vector<float> row;
        for (auto& val : jsonRow.GetArray()) {
            row.push_back(static_cast<float>(val.GetDouble()));
        }
        matrix.push_back(row);
    }

    return matrix;
}

std::vector<float> extractVectorData(const std::string& jsonData, const char* key) {
    std::vector<float> vector;
    rapidjson::Document doc;

    if (doc.Parse(jsonData.c_str()).HasParseError()) {
        std::cerr << "Error parsing JSON data" << std::endl;
        return vector;
    }

    if (!doc.HasMember(key) || !doc[key].IsArray()) {
        std::cerr << "Invalid or missing vector data for key '" << key << "' in the JSON." << std::endl;
        return vector;
    }

    const rapidjson::Value& jsonVector = doc[key];
    for (auto& val : jsonVector.GetArray()) {
        vector.push_back(static_cast<float>(val.GetDouble()));
    }

    return vector;
}


// Helper function to compute a portion of the Jacobian matrix
void computeJacobianPortion(const std::vector<float>& output, 
                            const std::vector<float>& dvalues,
                            std::vector<std::vector<float>>& jacobian,
                            int startRow, 
                            int endRow) {
    for (int i = startRow; i < endRow; ++i) {
        for (size_t j = 0; j < output.size(); ++j) {
            jacobian[i][j] = (i == j ? 1 : 0) - output[i] * output[j];
        }
    }
}


// std::vector<std::vector<float>> jacobianMatrix(const std::vector<float>& output, 
//                                                 const std::vector<float>& dvalues) {
//     if (output.size() != dvalues.size()) {
//         throw std::invalid_argument("Output and dvalues must be of the same size.");
//     }

//     size_t size = output.size();
//     std::vector<std::vector<float>> jacobian(size, std::vector<float>(size));

//     // Determine number of threads
//     unsigned int numThreads = std::thread::hardware_concurrency();
//     std::vector<std::thread> threads(numThreads);

//     // Divide the work among threads
//     int rowsPerThread = size / numThreads;
//     for (unsigned int i = 0; i < numThreads; ++i) {
//         int startRow = i * rowsPerThread;
//         int endRow = (i == numThreads - 1) ? size : startRow + rowsPerThread;
//         threads[i] = std::thread(computeJacobianPortion, std::ref(output), std::ref(dvalues), std::ref(jacobian), startRow, endRow);
//     }

//     // Join the threads
//     for (std::thread &t : threads) {
//         if (t.joinable()) {
//             t.join();
//         }
//     }

//     return jacobian;
// }




// Explicit instantiation
template bool saveToJSON<int>(const std::string& filename, const std::vector<std::vector<int>>& data);
template bool saveToJSON<float>(const std::string& filename, const std::vector<std::vector<float>>& data);
